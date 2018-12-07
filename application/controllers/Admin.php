<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin_model');
        $this->load->library('grocery_CRUD');
        $this->load->helper('security');
        $this->grocery_crud->set_theme('datatables');
        $function = $this->uri->segment('2');

        if ($this->session->userdata('admin_logged_in') !== true &&
            strtolower($function) != 'login') {
            redirect('admin/login');
        } elseif ($this->session->userdata('admin_logged_in') === true &&
            strtolower($function) == 'login') {
            redirect('admin/organizations');
        }
    }

    /*****************************************************************************/
    /**
     * index Function.
     *
     * @return mixed
     */
    /*****************************************************************************/
    public function index()
    {
        redirect('admin/organizations');
    }

    /*****************************************************************************/
    /**
     * Dachboard function.
     *
     * @return mixed
     */
    /*****************************************************************************/
    public function dashboard()
    {
        $this->admin_view('dashboard');
    }

    /*****************************************************************************/
    /**
     * Load the view.
     *
     * @return mixed
     */
    /*****************************************************************************/
    private function admin_view($view = null, $data = null)
    {
        $this->load->view('admin/includes/header', $data);
        $this->load->view('admin/' . $view, $data);
        $this->load->view('admin/includes/footer', $data);
    }

    /*****************************************************************************/
    /**
     * Login Function.
     *
     * @return mixed
     */
    /*****************************************************************************/
    public function login()
    {
        $data['auth_error'] = '';
        if ($this->input->post('admin_login')) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');
            if ($this->form_validation->run() !== false) {
                $username = preg_replace('/\s+/', '', $this->input->post('username'));
                $password = trim($this->input->post('password'));
                $userdata = $this->Admin_model->authenticate($username, $password);
                if ($userdata === false) {
                    $data['auth_error'] = 'Wrong username or password';
                } else {
                    $this->session->set_userdata(array('logo' => $userdata['logo'],
                        'admin_username' => $userdata['username'],
                        'admin_full_name' => $userdata['full_name'],
                        'admin_user_id' => $userdata['id'], 'admin_logged_in' => true,));

                    $this->session->set_flashdata('notification_success',
                        'You have successfully logged in.');


                    redirect('admin/organizations');
                }
            }
        }
        $this->load->view('admin/login', $data);
    }

    /*****************************************************************************/
    /**
     * Logout and unset all session variables.
     *
     * @return mixed
     */
    /*****************************************************************************/
    public function logout()
    {
        if ($this->session->userdata('admin_logged_in') === true) {
            $this->session->sess_destroy();
            $this->session->unset_userdata(array('admin_logged_in', 'admin_username',
                'admin_full_name', 'logo', 'admin_user_id',));
            redirect('admin/login');
        } else {
            redirect('404');
        }
    }

    /*****************************************************************************/
    /**
     * Admin Management Module "List, Edit, Create users".
     *
     * @return mixed
     */
    /*****************************************************************************/
    public function adminUsers()
    {
        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table('admin_users');

        $crud->set_subject('Admin User');
        $crud->fields('username', 'password', 'full_name', 'email', 'default_user', 'logo');


        $crud->set_field_upload('logo', 'assets/nav');

        $crud->columns('username', 'full_name', 'email', 'logo');
        $crud->order_by('id', 'desc');

        //rules for validating inputs
        $crud->set_rules('username', 'Username', 'xss_clean|trim|required|callback_username_check');

        $state = $crud->getState();


        if (($state == 'add') OR ($state == 'insert_validation')) {
            $crud->set_rules('password', 'Password', 'xss_clean|min_length[8]|max_length[25]|trim|required|callback_is_password_strong_add');
            $crud->required_fields('username', 'password', 'full_name', 'email', 'logo');
        } elseif (($state == 'edit') OR ($state == 'update_validation')) {
            $crud->set_rules('password', 'Password', 'xss_clean|min_length[8]|max_length[25]|trim|callback_is_password_strong_edit');
            $crud->set_js('assets/grocery_crud/js/myCustom.js');
            $crud->required_fields('username', 'full_name', 'email', 'logo');
        }

        $crud->set_rules('email', 'Email', 'xss_clean|valid_email|trim|required|callback_email_check');

        $crud->set_rules('full_name', 'Full Name', 'xss_clean|required');
        $crud->set_rules('logo', 'Logo', 'xss_clean|required');


        $crud->change_field_type('password', 'password');

        $crud->display_as('default_user', 'Default user');

        // callback for hashing password before insert
        $crud->callback_before_insert(function ($post_array) {
            $post_array['password'] = md5($post_array['password']);

            return $post_array;
        });
        // callback for hashing password before update
        $crud->callback_before_update(function ($post_array) {

            if ($post_array['password'] == '') {
                unset($post_array['password']);
            } else {
                $post_array['password'] = md5($post_array['password']);
            }

            $this->session->set_userdata(array('logo' => $post_array['logo'],
                'admin_username' => $post_array['username'],
                'admin_full_name' => $post_array['full_name'],
                'admin_logged_in' => true,));

            return $post_array;
        });

        // callback for check default user before delete
        $crud->callback_before_delete(array($this, 'default_user_before_delete'));
        $crud->set_lang_string('delete_error_message', 'Default user can not be deleted.');

        //callback for upload valid image
        $crud->callback_before_upload(array($this, '_callback_check_image'));

        $crud->unset_read();


        //$data['order_by'] = 'id';
        $data['page_title'] = 'Admin Users';
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
    }


    public function default_user_before_delete($primary_key)
    {
        $default_user = $this->db->where("id", $primary_key)->get('admin_users')->row()->default_user;
        echo $default_user;

        if ($default_user !== 'yes') {
            return TRUE;
        } else {
            return FALSE;
        }


    }

    //     callback to check image
    public function _callback_check_image($files_to_upload)
    {

        foreach ($files_to_upload as $file) {
            $tmp_name = $file['tmp_name'];

            if (exif_imagetype($tmp_name) != (IMAGETYPE_PNG || IMAGETYPE_JPEG || IMAGETYPE_PNG || IMAGETYPE_GIF)) {
                return "This is not an image, please choose an image file";
            } else {
                return TRUE;
            }

        }

    }

// callback to check unique username
    public function username_check($str)
    {
        $id = $this->uri->segment(4);
        if (!empty($id) && is_numeric($id)) {
            $username_old = $this->db->where("id", $id)->get('admin_users')->row()->username;
            $this->db->where("username !=", $username_old);
        }

        $num_row = $this->db->where('username', $str)->get('admin_users')->num_rows();
        if ($num_row >= 1) {
            $this->form_validation->set_message('username_check', 'The username already exists');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    // callback to check unique username
    public function email_check($str)
    {
        $id = $this->uri->segment(4);
        if (!empty($id) && is_numeric($id)) {
            $username_old = $this->db->where("id", $id)->get('admin_users')->row()->email;
            $this->db->where("email !=", $username_old);
        }

        $num_row = $this->db->where('email', $str)->get('admin_users')->num_rows();
        if ($num_row >= 1) {
            $this->form_validation->set_message('email_check', 'The email already exists');
            return FALSE;
        } else {
            return TRUE;
        }
    }

// callback function for validating strong password
    public function is_password_strong_add($password)
    {
        if (preg_match('#[0-9]#', $password) && preg_match('#[a-zA-Z]#', $password)) {
            return true;
        }

        $this->form_validation->set_message('is_password_strong_add', 'Week Password');

        return false;
    }

    public function is_password_strong_edit($password)
    {
        if ((preg_match('#[0-9]#', $password) && preg_match('#[a-zA-Z]#', $password)) || $password == '' || empty($password)) {
            return true;
        }

        $this->form_validation->set_message('is_password_strong_edit', 'Week Password');

        return false;
    }

    /*****************************************************************************/
    /**
     * Job Module Add Edit delete.
     *
     * @return mixed
     */
    /*****************************************************************************/
    public function jobs()
    {
        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table('jobs');
        $crud->set_subject('Jobs Module');
        $crud->fields('sector', 'job_title', 'type', 'reveive_mails');
        $crud->required_fields('sector', 'job_title', 'type', 'reveive_mails');
        $crud->order_by('id', 'desc');

        $crud->display_as('reveive_mails', 'Receive mails');

        //validation rules
        $crud->set_rules('sector', 'Sector', 'xss_clean|required');
        $crud->set_rules('job_title', 'Job Title', 'xss_clean|required');
        $crud->set_rules('type', 'Type', 'xss_clean|required');

        $crud->columns('sector', 'job_title', 'type', 'reveive_mails');
        $data['page_title'] = 'Jobs Module';
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
    }

    /*****************************************************************************/
    /**
     * Question Groups Module Add, Edit, Delete.
     *
     * @return mixed
     */
    /*****************************************************************************/
    public function groups()
    {
        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table('groups');
        $crud->set_subject('Group of Questions');
        $crud->order_by('id', 'desc');
        $crud->fields('group_name', 'group_alias', 'type', 'sector', 'jobs');
        $crud->display_as('group_name', 'Group name');
        $crud->required_fields('group_name', 'group_alias', 'type', 'sector');
        $crud->columns('group_name', 'group_alias', 'type', 'sector');
        $crud->set_relation_n_n('jobs', 'jobs_groups', 'jobs',
            'GroupID', 'jobID', 'job_title');

        //validation rules
        $crud->set_rules('sector', 'Sector', 'xss_clean|required');
        $crud->set_rules('group_name', 'Group Name', 'xss_clean|required');
        $crud->set_rules('type', 'Type', 'xss_clean|required');
        $crud->set_rules('group_alias', 'group alias', 'xss_clean|required');

        $crud->set_relation('sector', 'groupsSectors', 'Sector');

        $data['page_title'] = 'Groups of Questions Management';
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
    }

    /*****************************************************************************/
    /**
     * Question Module Add, Edit, Delete.
     *
     * @return mixed
     */
    /*****************************************************************************/
    public function questions()
    {
        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table('questions');
        $crud->set_subject('Questions Management');
        $crud->order_by('id', 'desc');
        $crud->fields('quesition', 'group_id', 'hidden');
        $crud->display_as('quesition', 'Question')->display_as('group_id', 'Group Name');
        $crud->required_fields('quesition', 'group_id');

        $crud->columns('sector', 'group_id', 'quesition', 'organization_id', 'hidden');
        $crud->callback_column('sector', array($this, '_callback_getSector'));

        //validation rules
        $crud->set_rules('quesition', 'Quesition', 'xss_clean|required');
        $crud->set_rules('group_id', 'Group Name', 'xss_clean|required');

        $crud->set_relation('group_id', 'groups', '{group_name}');
        $crud->set_relation('organization_id', 'organizations', 'organization_name');

        $crud->callback_delete(array($this,'delete_question'));

        $crud->display_as('organization_id', 'Organization Name');
        $crud->where('deleted','!= 1');

        $data['page_title'] = 'Questions Management';
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
    }

    public function delete_question($primary_key)
    {
        return $this->db->update('questions',array('deleted' => '1'),array('id' => $primary_key));
    }

    //Callback for viewing the sector Name related to the question
    public function _callback_getSector($value, $row)
    {
        $this->db->select('groupsSectors.Sector');
        $this->db->from('groupsSectors');
        $this->db->join('groups', 'groups.sector = groupsSectors.id', 'left');
        $this->db->where(array('groups.id' => $row->group_id));
        $return = $this->db->get()->row_array();

        return $return['Sector'];
    }

    /*****************************************************************************/
    /**
     * Organization Module Add, Edit, Delete
     * many to many relation between oranization and inhouse service and between
     *    organization and online services.
     *
     * @return mixed
     */
    /*****************************************************************************/
    public function organizations()
    {
        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table('organizations');
        $crud->set_subject('Organizations Management');
        $crud->order_by('id', 'desc');
        $crud->set_relation_n_n('online', 'organizationToOnline', 'online_service',
            'organization_id', 'serviceID', 'service');
        $crud->set_relation_n_n('inhouse', 'organizationToInhouse', 'inhouse_service',
            'organization_id', 'serviceID', 'service');

        $crud->fields('organization_name', 'email', 'password', 'numberOfBranches', 'numberOfUsers', 'allowedBranches',
            'allowedUsers', 'DayToExpire', 'online', 'inhouse', 'smsGatway', 'logo', 'splash_background_color',
            'branches_background_color', 'branches_title_color', 'branches_text_color',
            'login_background_image', 'login_background_color', 'login_settings_icon', 'login_text_color', 'login_phone_icon', 'login_color_button',
            'welcome_background_color', 'welcome_title_color', 'welcome_text_color',
            'thanks_background_color', 'thanks_text_color',
            'settings_background_color', 'settings_title_color', 'settings_text_color', 'settings_button_text_color', 'settings_background_button_color',
            'feedback_background_image', 'feedback_title_background_color', 'feedback_title_color', 'feedback_headers_background_color',
            'feedback_headers_color', 'feedback_dropdown_background_color', 'feedback_dropdown_text_color', 'feedback_rating_color',
            'feedback_acceptable_image', 'feedback_acceptable_selected_image', 'feedback_good_image', 'feedback_good_selected_image', 'feedback_excellent_image',
            'feedback_excellent_selected_image', 'feedback_rating_text_color', 'feedback_separator_color', 'feedback_bottom_button_color',
            'feedback_bottom_text_color', 'feedback_comment_background_image');

        $crud->change_field_type('password', 'password');
        $crud->change_field_type('numberOfBranches', 'hidden');
        $crud->change_field_type('numberOfUsers', 'hidden');
        //validation rules
        $state = $crud->getState();
        $crud->set_rules('email', 'Email', 'xss_clean|valid_email|trim|required|callback_organ_email_check');

        if (($state == 'add') OR ($state == 'insert_validation')) {
            $crud->set_rules('password', 'Password', 'xss_clean|min_length[8]|max_length[25]|trim|required|callback_is_password_strong_add');
            $crud->required_fields('organization_name', 'email', 'password', 'allowedBranches',
                'allowedUsers', 'DayToExpire', 'online', 'inhouse', 'smsGatway', 'logo');
        } elseif (($state == 'edit') OR ($state == 'update_validation')) {
            $crud->set_rules('password', 'Password', 'xss_clean|min_length[8]|max_length[25]|trim|callback_is_password_strong_edit');
            $crud->set_js('assets/grocery_crud/js/myCustom.js');
            $crud->required_fields('organization_name', 'email', 'allowedBranches',
                'allowedUsers', 'DayToExpire', 'online', 'inhouse', 'smsGatway', 'logo');
        }

        $crud->set_rules('organization_name', 'Organization Name', 'xss_clean|required');
        $crud->set_rules('logo', 'Logo', 'xss_clean|required');
        $crud->set_rules('allowedBranches', 'Allowed Branches', 'xss_clean|required');
        $crud->set_rules('allowedUsers', 'Allowed Employees', 'xss_clean|required');
        $crud->set_rules('DayToExpire', 'Day to Expire', 'xss_clean|required');
        $crud->set_rules('smsGatway', 'SMS Gatway Service', 'xss_clean|required');
        $crud->set_rules('inhouse', 'Inhouse', 'xss_clean|callback_is_emptyInhouse');
        $crud->set_rules('online', 'Online', 'xss_clean|callback_is_emptyOnline');

        $crud->set_field_upload('logo', 'assets/nav');
        $crud->set_field_upload('login_background_image', 'assets/nav');
        $crud->set_field_upload('login_settings_icon', 'assets/nav');
        $crud->set_field_upload('login_phone_icon', 'assets/nav');
        $crud->set_field_upload('feedback_background_image', 'assets/nav');
        $crud->set_field_upload('feedback_acceptable_image', 'assets/nav');
        $crud->set_field_upload('feedback_acceptable_selected_image', 'assets/nav');
        $crud->set_field_upload('feedback_good_image', 'assets/nav');
        $crud->set_field_upload('feedback_good_selected_image', 'assets/nav');
        $crud->set_field_upload('feedback_excellent_image', 'assets/nav');
        $crud->set_field_upload('feedback_excellent_selected_image', 'assets/nav');
        $crud->set_field_upload('feedback_comment_background_image', 'assets/nav');

        $crud->columns('id', 'organization_name', 'email', 'allowedBranches', 'numberOfBranches','allowedUsers','numberOfUsers', 'DayToExpire');
        $crud->field_type('online', 'multiselect');
        $crud->field_type('inhouse', 'multiselect');

        $crud->set_js('assets/new/jscolor.js');

        $crud->callback_field('splash_background_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="splash_background_color">';
            return $return;
        });

        $crud->callback_field('branches_background_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="branches_background_color">';
            return $return;
        });

        $crud->callback_field('branches_title_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="branches_title_color">';
            return $return;
        });

        $crud->callback_field('branches_text_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="branches_text_color">';
            return $return;
        });

        $crud->callback_field('login_background_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="login_background_color">';
            return $return;
        });

        $crud->callback_field('login_text_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="login_text_color">';
            return $return;
        });

        $crud->callback_field('login_color_button', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="login_color_button">';
            return $return;
        });

        $crud->callback_field('welcome_background_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="welcome_background_color">';
            return $return;
        });

        $crud->callback_field('welcome_title_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="welcome_title_color">';
            return $return;
        });

        $crud->callback_field('welcome_text_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="welcome_text_color">';
            return $return;
        });

        $crud->callback_field('thanks_background_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="thanks_background_color">';
            return $return;
        });

        $crud->callback_field('thanks_text_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="thanks_text_color">';
            return $return;
        });


        $crud->callback_field('settings_background_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="settings_background_color">';
            return $return;
        });

        $crud->callback_field('settings_title_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="settings_title_color">';
            return $return;
        });

        $crud->callback_field('settings_text_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="settings_text_color">';
            return $return;
        });

        $crud->callback_field('settings_button_text_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="settings_button_text_color">';
            return $return;
        });

        $crud->callback_field('settings_background_button_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="settings_background_button_color">';
            return $return;
        });

        $crud->callback_field('feedback_title_background_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="feedback_title_background_color">';
            return $return;
        });

        $crud->callback_field('feedback_title_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="feedback_title_color">';
            return $return;
        });

        $crud->callback_field('feedback_headers_background_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="feedback_headers_background_color">';
            return $return;
        });

        $crud->callback_field('feedback_headers_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="feedback_headers_color">';
            return $return;
        });

        $crud->callback_field('feedback_dropdown_background_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="feedback_dropdown_background_color">';
            return $return;
        });

        $crud->callback_field('feedback_dropdown_text_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="feedback_dropdown_text_color">';
            return $return;
        });

        $crud->callback_field('feedback_rating_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="feedback_rating_color">';
            return $return;
        });

        $crud->callback_field('feedback_rating_text_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="feedback_rating_text_color">';
            return $return;
        });

        $crud->callback_field('feedback_separator_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="feedback_separator_color">';
            return $return;
        });
        $crud->callback_field('feedback_bottom_button_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="feedback_bottom_button_color">';
            return $return;
        });

        $crud->callback_field('feedback_bottom_text_color', function ($value = '') {
            $return = '<input class="jscolor {hash:true}" value="' . (($value) ? $value : '') . '" maxlength="7" type="text" name="feedback_bottom_text_color">';
            return $return;
        });


        //callback for upload valid image
        $crud->callback_before_upload(array($this, '_callback_check_image'));

        $crud->display_as('organization_name', 'Organization Name')->
        display_as('allowedBranches', 'Allowed Branches')->
        display_as('allowedUsers', 'Allowed Employees')->
        display_as('numberOfBranches', 'Remaining Branches')->
        display_as('numberOfUsers', 'Remaining Employees')->
        display_as('DayToExpire', 'Expiry Date')->
        display_as('smsGatway', 'SMS Gatway Service');

        //Callback to hash the password before insert
        $crud->callback_before_insert(function ($post_array) {
            $post_array['password'] = md5($post_array['password']);
            $post_array['numberOfBranches'] = $post_array['allowedBranches'];
            $post_array['numberOfUsers'] = $post_array['allowedUsers'];

            return $post_array;
        });
        //Callback to hash the password before update
        $crud->callback_before_update(function ($post_array) {
            if ($post_array['password'] == '') {
                unset($post_array['password']);

            } else {
                $post_array['password'] = md5($post_array['password']);
            }

            $post_array['numberOfBranches'] = $post_array['allowedBranches'];
            $post_array['numberOfUsers'] = $post_array['allowedUsers'];

            return $post_array;
        });
        //calling callback after insertion to copy question
        $crud->callback_after_insert(array($this, '_callback_get_lastInsertID'));

        $crud->unset_read();
        $data['page_title'] = 'Organizations Management';
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
    }


    // callback to check unique username
    public function organ_email_check($str)
    {
        $id = $this->uri->segment(4);
        if (!empty($id) && is_numeric($id)) {
            $username_old = $this->db->where("id", $id)->get('organizations')->row()->email;
            $this->db->where("email !=", $username_old);
        }

        $num_row = $this->db->where('email', $str)->get('organizations')->num_rows();
        if ($num_row >= 1) {
            $this->form_validation->set_message('organ_email_check', 'The email already exists');
            return FALSE;
        } else {
            return TRUE;
        }
    }


    // callback function for validating empty Inhouse services
    public function is_emptyInhouse($inhouse)
    {
        $inhouse = $this->input->post('inhouse');

        if (!empty($inhouse)) {
            return true;
        }
        $this->form_validation->set_message('is_emptyInhouse', 'Please Select required Inhouse services');
        return false;
    }

    // callback function for validating empty Online services
    public function is_emptyOnline($online)
    {
        $online = $this->input->post('online');
        if (!empty($online)) {
            return true;
        }
        $this->form_validation->set_message('is_emptyOnline', 'Please Select required Online services');
        return false;
    }

    /*callback after inserting an Organization to copy all Question of iconnect
    with organization_id = Null*/
    public function _callback_get_lastInsertID($post_array, $primary_key)
    {
        $sql = 'select * from questions where organization_id is NULL';
        $query = $this->db->query($sql);
        $query = $query->result();
        if (sizeof($query) > 0) {
            foreach ($query as $row) {
                $row->organization_id = $primary_key;
                unset($row->id);
                $this->db->insert('questions', $row);
            }
        }
    }

    /*****************************************************************************/
    /**
     * Branches module edit add delete, with callbacks for setting maps lan and long
     * ajax to change Online and Services once selecting an organization
     * ajax to check the avaiable number of branches.
     *
     * @return mixed
     */
    /*****************************************************************************/
    public function branches()
    {
        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table('branches');
        $crud->set_subject('Branches Management');
        $crud->order_by('id', 'desc');

        $crud->fields('bran_name', 'map', 'lat', 'lng', 'organization_id', 'online', 'inhouse', 'uniqueID');
        $crud->display_as('bran_name', 'Branch name')
            ->display_as('map', 'Pin location')
            ->display_as('lat', 'Latitude')
            ->display_as('lng', 'Longitude')
            ->display_as('organization_id', 'Organization Name');

        $crud->set_relation_n_n('online', 'branchesToOnline', 'online_service',
            'branch_id', 'serviceID', 'service');
        $crud->set_relation_n_n('inhouse', 'branchesToInhouse', 'inhouse_service',
            'branch_id', 'serviceID', 'service');
        $crud->set_relation('organization_id', 'organizations', 'organization_name');

        $crud->required_fields('bran_name', 'lat', 'lng', 'organization_id', 'online', 'inhouse');
        $crud->columns('bran_name', 'organization_id');

        /*Adding Map and lat & lng fields to hold the clicked position on the map
        And assigning Map.js to handle click on the map*/
        $crud->change_field_type('lat', 'hidden');
        $crud->change_field_type('lng', 'hidden');
        $crud->change_field_type('uniqueID', 'hidden');

        $crud->callback_field('map', function () {
            return '<p>Refine your position by dragging and dropping the pin on your location</p>
            <input id="pac-input" class="controls" type="text" placeholder="Search Box" style="top: 15px;width: 250px;right: 10px;">
    				<div id="retailer-map" style="width:530px; height:300px;"></div>';
        });
        if ($crud->getState() == 'add' || $crud->getState() == 'edit' || $crud->getState() == 'copy') {
            $crud->set_js('assets/new/map.js');
        } elseif ($crud->getState() == 'read') {
            $crud->set_js('assets/new/map.js');
        }

        //callbacks to unset map fields before insert and update
        $crud->callback_before_insert(array($this, 'unset_map_field_add'));
        $crud->callback_before_update(array($this, 'unset_map_field_edit'));

        /*callback after insert to hash password and decrement the current number
        of branches for the organization*/
        $crud->callback_after_insert(function ($post_array) {
            $this->db->where('id', $post_array['organization_id']);
            $this->db->where('numberOfBranches >', 0);
            $this->db->set('numberOfBranches', 'numberOfBranches-1', false);
            $this->db->update('organizations');
        });
        $crud->callback_before_delete(array($this, 'branch_before_delete'));


        //check at insertion the selected inhouse and online are related to the organization
//        if ($crud->getState() == 'insert_validation') {
//
//            $crud->set_rules('organization_id', 'Organization', 'callback_validate_orgid');
//        }


        $crud->set_rules('bran_name', 'Branch name', 'xss_clean|required');
        $crud->set_rules('lat', 'Latitude', 'xss_clean');
        $crud->set_rules('lng', 'Longitude', 'xss_clean');
        $crud->set_rules('organization_id', 'Organization Name', 'xss_clean|required|callback_validate_orgid');
        //call the upper callback of online and inhouse
        $crud->set_rules('online', 'Online', 'xss_clean|callback_is_emptyOnline');
        $crud->set_rules('inhouse', 'Inhouse', 'xss_clean|callback_is_emptyInhouse');

        $crud->unset_read();
        $data['page_title'] = 'Branches Management';
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
    }


    public function branch_before_delete($primary_key)
    {

        $org = $this->db->where("id", $primary_key)->get('branches')->row()->organization_id;

        $this->db->where('id', $org);
        $this->db->where('numberOfBranches >=', 0);
        $this->db->set('numberOfBranches', 'numberOfBranches+1', false);
        $this->db->update('organizations');
        return $primary_key . $org;
    }

    public function validate_orgid($orgid)
    {
        //check the selected services for the organization
        $inhouse = $this->input->post('inhouse');
        $online = $this->input->post('online');
        for ($i = 0; $i < sizeof($online); ++$i) {
            $query = $this->db->get_where('organizationToOnline', array(
                'organization_id' => $orgid,
                'serviceID' => $online[$i],
            ));
            $count = $query->num_rows();
            if ($count === 0) {
                $message = 'Invalid Online Service.';
                $this->form_validation->set_message('validate_orgid', $message);

                return false;
            }
        }
        //check the selected services for the organization
        for ($i = 0; $i < sizeof($inhouse); ++$i) {
            $query = $this->db->get_where('organizationToInhouse', array(
                'organization_id' => $orgid,
                'serviceID' => $inhouse[$i],
            ));
            $count = $query->num_rows();
            if ($count === 0) {
                $message = 'Invalid inhouse Service.';
                $this->form_validation->set_message('validate_orgid', $message);

                return false;
            }
        }

//        echo $this->uri->segment(3);
        if ($this->uri->segment(3) == 'insert_validation') {
            //check the allowed number of branches before inserting
            $this->db->select('numberOfBranches');
            $this->db->from('organizations');
            $this->db->where('id', $this->input->post('organization_id'));
            $query = $this->db->get()->result_array();
            if ($query[0]['numberOfBranches'] <= 0) {
                $message = 'This Organization consumed The permitted number of branches.';
                $this->form_validation->set_message('validate_orgid', $message);

                return false;
            }
        }
    }

    public function unset_map_field_add($post)
    {
        $post['uniqueID'] = strtoupper(uniqid());
        unset($post['field-map']);
        return $post;
    }

    public function unset_map_field_edit($post)
    {
        unset($post['field-map']);
    }

    //once the user select and organization ajax request run the function to get
    //services of the selected organization
    public function getOrgServicesAjax()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $orgID = $this->input->post('orgd');
            if ($orgID && is_numeric($orgID)) {
                $this->db->select('id,service');
                $this->db->from('inhouse_service');
                $this->db->join('organizationToInhouse', 'organizationToInhouse.serviceID = inhouse_service.id', 'left');
                $this->db->where('organization_id', $orgID);
                $query = $this->db->get()->result_array();
                $result['inhouse'] = $query;
                $this->db->select('id,service');
                $this->db->from('online_service');
                $this->db->join('organizationToOnline', 'organizationToOnline.serviceID = online_service.id', 'left');
                $this->db->where('organization_id', $orgID);
                $query = $this->db->get()->result_array();
                $result['online'] = $query;

                echo json_encode($result);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    /*****************************************************************************/
    /**
     * Employee module edit add delete
     * Callbacks to change Enum to radio buttons
     * Ajax to get brances assigned to organization
     * Ajax to check Job "Multi select or Single Select".
     *
     * @return mixed
     */
    /*****************************************************************************/
    public function employee()
    {
        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table('organizationEmployees');
        $crud->set_subject('Employees Management');
        $crud->order_by('id', 'desc');

        $crud->set_relation_n_n('branches', 'branchesToEmployes', 'branches',
            'employee_id', 'branch_id', 'bran_name');
        $crud->set_relation('organization_id', 'organizations', 'organization_name');
        $crud->set_relation('jobID', 'jobs', 'job_title');

        $crud->fields('employeName', 'email', 'password', 'logo', 'organization_id',
            'jobID', 'branches', 'type', 'questionSection', 'happinessSection', 'reportSection', 'suggestedBranchSection', 'screenSaverSection',
            'employeeSection', 'birthdateSection', 'backLogSection', 'customerBehaviourSection', 'suggestedBranchReport',
            'reviewsComments', 'webviewSection', 'employeeReport');

        $crud->change_field_type('password', 'password');

        //validation rules
        $state = $crud->getState();


        if (($state == 'add') OR ($state == 'insert_validation')) {
            $crud->set_rules('password', 'Password', 'xss_clean|min_length[8]|max_length[25]|trim|required|callback_is_password_strong_add');
            $crud->required_fields('employeName', 'email', 'password', 'organization_id',
                'jobID', 'type', 'branches');
        } elseif (($state == 'edit') OR ($state == 'update_validation')) {
            $crud->set_rules('password', 'Password', 'xss_clean|min_length[8]|max_length[25]|trim|callback_is_password_strong_edit');
            $crud->set_js('assets/grocery_crud/js/myCustom.js');
            $crud->required_fields('employeName', 'email', 'organization_id',
                'jobID', 'type', 'branches');
        }

        $crud->set_rules('email', 'Email', 'xss_clean|valid_email|trim|required|callback_emp_email_check');
        $crud->set_rules('branches', 'Branches', 'xss_clean|callback_validate_branches');
        $crud->set_rules('logo', 'Logo', 'xss_clean');
        $crud->set_rules('employeName', 'Employee Name', 'required|xss_clean|callback_emp_name_check');
        $crud->set_rules('organization_id', 'Organization Name', 'required|xss_clean');
        $crud->set_rules('jobID', 'Job', 'required|xss_clean');
        $crud->set_rules('type', 'Type', 'required|xss_clean');

        $crud->set_field_upload('logo', 'assets/nav');
        $crud->columns('employeName', 'email', 'organization_id', 'jobID', 'branches');
        $crud->field_type('branches', 'multiselect');
        $crud->display_as('email', 'Employee Email')->
        display_as('employeName', 'Employee Name')->
        display_as('logo', 'Employee Image')->
        display_as('organization_id', 'Organization Name')->
        display_as('jobID', 'Job');

        //callbacks for convertign fields to checkboxes
        $crud->callback_field('backLogSection', array($this, 'add_field_callback_1'));
        $crud->callback_field('birthdateSection', array($this, 'add_field_callback_2'));
        $crud->callback_field('employeeSection', array($this, 'add_field_callback_3'));
        $crud->callback_field('screenSaverSection', array($this, 'add_field_callback_4'));
        $crud->callback_field('suggestedBranchSection', array($this, 'add_field_callback_5'));
        $crud->callback_field('reportSection', array($this, 'add_field_callback_6'));
        $crud->callback_field('happinessSection', array($this, 'add_field_callback_7'));
        $crud->callback_field('questionSection', array($this, 'add_field_callback_8'));
        $crud->callback_field('customerBehaviourSection', array($this, 'add_field_callback_9'));
        $crud->callback_field('suggestedBranchReport', array($this, 'add_field_callback_10'));
        $crud->callback_field('reviewsComments', array($this, 'add_field_callback_11'));
        $crud->callback_field('webviewSection', array($this, 'add_field_callback_12'));
        $crud->callback_field('employeeReport', array($this, 'add_field_callback_13'));

        $crud->callback_before_delete(array($this, 'emp_before_delete'));

        //callbacks to hashing password before insert and update
        $crud->callback_before_insert(function ($post_array) {

            $post_array['password'] = md5($post_array['password']);

            if ($post_array['type'] == 'admin') {
                $post_array['backLogSection'] = '';
                $post_array['birthdateSection'] = '';
                $post_array['employeeSection'] = '';
                $post_array['screenSaverSection'] = '';
                $post_array['suggestedBranchSection'] = '';
                $post_array['reportSection'] = '';
                $post_array['happinessSection'] = '';
                $post_array['questionSection'] = '';
                $post_array['customerBehaviourSection'] = '';
                $post_array['suggestedBranchReport'] = '';
                $post_array['reviewsComments'] = '';
                $post_array['webviewSection'] = '';
                $post_array['employeeReport'] = '';
            }
            return $post_array;
        });
        $crud->callback_before_update(function ($post_array) {
            if ($post_array['password'] == '') {
                unset($post_array['password']);
            } else {
                $post_array['password'] = md5($post_array['password']);
            }
            if ($post_array['type'] == 'admin') {
                $post_array['backLogSection'] = '';
                $post_array['birthdateSection'] = '';
                $post_array['employeeSection'] = '';
                $post_array['screenSaverSection'] = '';
                $post_array['suggestedBranchSection'] = '';
                $post_array['reportSection'] = '';
                $post_array['happinessSection'] = '';
                $post_array['questionSection'] = '';
                $post_array['customerBehaviourSection'] = '';
                $post_array['suggestedBranchReport'] = '';
                $post_array['reviewsComments'] = '';
                $post_array['webviewSection'] = '';
                $post_array['employeeReport'] = '';
            }
            return $post_array;
        });

        //callback to decrement the number of allowed users after inserting
        $crud->callback_after_insert(function ($post_array) {
            $this->db->where('id', $post_array['organization_id']);
            $this->db->where('numberOfUsers >', 0);
            $this->db->set('numberOfUsers', 'numberOfUsers-1', false);
            $this->db->update('organizations');
        });

        //callback for upload valid image
        $crud->callback_before_upload(array($this, '_callback_check_image'));

        $crud->unset_read();
        $data['page_title'] = 'Employees Management';
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
    }

    public function emp_before_delete($primary_key)
    {

        $org = $this->db->where("id", $primary_key)->get('organizationEmployees')->row()->organization_id;

        $this->db->where('id', $org);
        $this->db->where('numberOfUsers >=', 0);
        $this->db->set('numberOfUsers', 'numberOfUsers+1', false);
        $this->db->update('organizations');
        return $primary_key . $org;
    }

    // callback to check unique email
    public function emp_email_check($str)
    {
        $orgid = $this->input->post('organization_id');
        if (is_null($orgid)) {
            $this->form_validation->set_message('emp_email_check', 'Please Select an Organization');
            return false;
        }
        $id = $this->uri->segment(4);
        if (!empty($id) && is_numeric($id)) {
            $name_old = $this->db->where(array("id" => $id, 'organization_id' => $orgid))->get('organizationEmployees')->row()->email;
            if ($str == $name_old) {
                return true;
            } else {
                $query = $this->db->get_where('organizationEmployees', array('email' => $str, 'organization_id' => $orgid));
                if ($query->num_rows() > 0) {
                    $this->form_validation->set_message('emp_email_check', 'The Employee email already exists');
                    return false;
                } else {
                    return true;
                }
            }
        } else {
            $query = $this->db->get_where("organizationEmployees", array('email' => $str, 'organization_id' => $orgid));
            if ($query->num_rows() > 0) {
                $this->form_validation->set_message('emp_email_check', 'The Employee email already exists');
                return false;
            } else {
                return true;
            }
        }
    }

    // callback to check unique email
    public function emp_name_check($str)
    {
        $orgid = $this->input->post('organization_id');
        if (is_null($orgid)) {
            $this->form_validation->set_message('emp_email_check', 'Please Select an Organization');
            return false;
        }
        $id = $this->uri->segment(4);
        if (!empty($id) && is_numeric($id)) {
            $name_old = $this->db->where(array("id" => $id, 'organization_id' => $orgid))->get('organizationEmployees')->row()->employeName;
            if ($str == $name_old) {
                return true;
            } else {
                $query = $this->db->get_where('organizationEmployees', array('employeName' => $str, 'organization_id' => $orgid));
                if ($query->num_rows() > 0) {
                    $this->form_validation->set_message('emp_name_check', 'The Employee Name already exists');
                    return false;
                } else {
                    return true;
                }
            }
        } else {
            $query = $this->db->get_where("organizationEmployees", array('employeName' => $str, 'organization_id' => $orgid));
            if ($query->num_rows() > 0) {
                $this->form_validation->set_message('emp_name_check', 'The Employee Name already exists');
                return false;
            } else {
                return true;
            }
        }
    }

    public function add_field_callback_1($value = '')
    {
        $return = ' <input type="radio" name="backLogSection" value="edit" ';
        $return .= ($value == 'edit') ? 'checked' : '';
        $return .= '/> Edit
		<input type="radio" name="backLogSection" value="view"';
        $return .= ($value == 'view') ? 'checked' : '';
        $return .= '/> View 
		<input type="radio" name="backLogSection" value=""';
        $return .= ($value == '') ? 'checked' : '';
        $return .= '/> Not premitted';

        return $return;
    }

    public function add_field_callback_2($value = '')
    {
        $return = ' <input type="radio" name="birthdateSection" value="edit" ';
        $return .= ($value == 'edit') ? 'checked' : '';
        $return .= '/> Edit
		<input type="radio" name="birthdateSection" value="view"';
        $return .= ($value == 'view') ? 'checked' : '';
        $return .= '/> View
		<input type="radio" name="birthdateSection" value=""';
        $return .= ($value == '') ? 'checked' : '';
        $return .= '/> Not premitted';
        return $return;
    }

    public function add_field_callback_3($value = '')
    {
        $return = ' <input type="radio" name="employeeSection" value="edit" ';
        $return .= ($value == 'edit') ? 'checked' : '';
        $return .= '/> Edit
		<input type="radio" name="employeeSection" value="view"';
        $return .= ($value == 'view') ? 'checked' : '';
        $return .= '/> View 
		<input type="radio" name="employeeSection" value=""';
        $return .= ($value == '') ? 'checked' : '';
        $return .= '/> Not premitted';
        return $return;
    }

    public function add_field_callback_4($value = '')
    {
        $return = ' <input type="radio" name="screenSaverSection" value="edit" ';
        $return .= ($value == 'edit') ? 'checked' : '';
        $return .= '/> Edit
		<input type="radio" name="screenSaverSection" value="view"';
        $return .= ($value == 'view') ? 'checked' : '';
        $return .= '/> View 
		<input type="radio" name="screenSaverSection" value=""';
        $return .= ($value == '') ? 'checked' : '';
        $return .= '/> Not premitted';
        return $return;
    }

    public function add_field_callback_5($value = '')
    {
        $return = ' <input type="radio" name="suggestedBranchSection" value="edit" ';
        $return .= ($value == 'edit') ? 'checked' : '';
        $return .= '/> Edit
		<input type="radio" name="suggestedBranchSection" value="view"';
        $return .= ($value == 'view') ? 'checked' : '';
        $return .= '/> View 
		<input type="radio" name="suggestedBranchSection" value=""';
        $return .= ($value == '') ? 'checked' : '';
        $return .= '/> Not premitted';
        return $return;
    }

    public function add_field_callback_6($value = '')
    {
        $return = ' <input type="radio" name="reportSection" value="edit" ';
        $return .= ($value == 'edit') ? 'checked' : '';
        $return .= '/> Edit
		<input type="radio" name="reportSection" value="view"';
        $return .= ($value == 'view') ? 'checked' : '';
        $return .= '/> View 
		<input type="radio" name="reportSection" value=""';
        $return .= ($value == '') ? 'checked' : '';
        $return .= '/> Not premitted';
        return $return;
    }

    public function add_field_callback_7($value = '')
    {
        $return = ' <input type="radio" name="happinessSection" value="edit" ';
        $return .= ($value == 'edit') ? 'checked' : '';
        $return .= '/> Edit
		<input type="radio" name="happinessSection" value="view"';
        $return .= ($value == 'view') ? 'checked' : '';
        $return .= '/> View 
		<input type="radio" name="happinessSection" value=""';
        $return .= ($value == '') ? 'checked' : '';
        $return .= '/> Not premitted';
        return $return;
    }

    public function add_field_callback_8($value = '')
    {
        $return = ' <input type="radio" name="questionSection" value="edit" ';
        $return .= ($value == 'edit') ? 'checked' : '';
        $return .= '/> Edit
		<input type="radio" name="questionSection" value="view"';
        $return .= ($value == 'view') ? 'checked' : '';
        $return .= '/> View 
		<input type="radio" name="questionSection" value=""';
        $return .= ($value == '') ? 'checked' : '';
        $return .= '/> Not premitted';
        return $return;
    }

    public function add_field_callback_9($value = '')
    {
        $return = ' <input type="radio" name="customerBehaviourSection" value="edit" ';
        $return .= ($value == 'edit') ? 'checked' : '';
        $return .= '/> Edit
		<input type="radio" name="customerBehaviourSection" value="view"';
        $return .= ($value == 'view') ? 'checked' : '';
        $return .= '/> View 
		<input type="radio" name="customerBehaviourSection" value=""';
        $return .= ($value == '') ? 'checked' : '';
        $return .= '/> Not premitted';
        return $return;
    }

    public function add_field_callback_10($value = '')
    {
        $return = ' <input type="radio" name="suggestedBranchReport" value="edit" ';
        $return .= ($value == 'edit') ? 'checked' : '';
        $return .= '/> Edit
		<input type="radio" name="suggestedBranchReport" value="view"';
        $return .= ($value == 'view') ? 'checked' : '';
        $return .= '/> View 
		<input type="radio" name="suggestedBranchReport" value=""';
        $return .= ($value == '') ? 'checked' : '';
        $return .= '/> Not premitted';
        return $return;
    }

    public function add_field_callback_11($value = '')
    {
        $return = ' <input type="radio" name="reviewsComments" value="edit" ';
        $return .= ($value == 'edit') ? 'checked' : '';
        $return .= '/> Edit
		<input type="radio" name="reviewsComments" value="view"';
        $return .= ($value == 'view') ? 'checked' : '';
        $return .= '/> View 
		<input type="radio" name="reviewsComments" value=""';
        $return .= ($value == '') ? 'checked' : '';
        $return .= '/> Not premitted';
        return $return;
    }

    public function add_field_callback_12($value = '')
    {
        $return = ' <input type="radio" name="webviewSection" value="edit" ';
        $return .= ($value == 'edit') ? 'checked' : '';
        $return .= '/> Edit
		<input type="radio" name="webviewSection" value="view"';
        $return .= ($value == 'view') ? 'checked' : '';
        $return .= '/> View 
		<input type="radio" name="webviewSection" value=""';
        $return .= ($value == '') ? 'checked' : '';
        $return .= '/> Not premitted';
        return $return;
    }

    public function add_field_callback_13($value = '')
    {
        $return = ' <input type="radio" name="employeeReport" value="edit" ';
        $return .= ($value == 'edit') ? 'checked' : '';
        $return .= '/> Edit
		<input type="radio" name="employeeReport" value="view"';
        $return .= ($value == 'view') ? 'checked' : '';
        $return .= '/> View 
		<input type="radio" name="employeeReport" value=""';
        $return .= ($value == '') ? 'checked' : '';
        $return .= '/> Not premitted';
        return $return;
    }

    public function validate_branches($branches)
    {
        $branches = $this->input->post('branches');
        //validating the selected branches check if its related to the organization
        if (empty($this->input->post('branches'))) {
            $message = 'The Branches field is required';
            $this->form_validation->set_message('validate_branches', $message);

            return false;
        } else {
            foreach ($branches as $branch) {
                $this->db->select('*');
                $this->db->from('branches');
                $this->db->where('organization_id', $this->input->post('organization_id'));
                $this->db->where('id', $branch);
                $query = $this->db->get()->result_array();
                if (empty($query)) {
                    $message = 'Invalid Branches';
                    $this->form_validation->set_message('validate_branches', $message);
                }
            }
        }
        if ($this->uri->segment(3) == 'insert_validation') {
            //check the allowed balance of users for the organization
            $this->db->select('numberOfUsers');
            $this->db->from('organizations');
            $this->db->where('id', $this->input->post('organization_id'));
            $query = $this->db->get()->result_array();
            if ($query[0]['numberOfUsers'] <= 0) {
                $message = 'This Organization consumed The permitted number of employees.';
                $this->form_validation->set_message('validate_branches', $message);

                return false;
            }
        }
    }

    //Ajax to get branches of the selected branches
    public function getBranchesAjax()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $orgID = $this->input->post('orgd');
            if ($orgID && is_numeric($orgID)) {
                $this->db->select('branches.id,branches.bran_name');
                $this->db->from('branches');
                $this->db->join('organizations', 'organizations.id = branches.organization_id', 'left');
                $this->db->where('organization_id', $orgID);
                $query = $this->db->get()->result_array();
                $result['branches'] = $query;
                echo json_encode($result);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    //Ajax to check the job type
    public function checkjob()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $jobiD = $this->input->post('jobi');
            if ($jobiD && is_numeric($jobiD)) {
                $this->db->select('type');
                $this->db->from('jobs');
                $this->db->where('id', $jobiD);
                $query = $this->db->get()->result_array();
                $result = array('type' => $query[0]['type']);
                echo json_encode($result);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
}
