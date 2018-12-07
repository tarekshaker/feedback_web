<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Service_model');
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'ifeedbackalert@pitechnologies.net',
            'smtp_pass' => '20s5m19s95m',
            'mailtype' => 'html',
            'charset' => 'utf-8'
        );
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
    }

///////////////////////////////////////////////////////////////////////////////
    public function getconfigs()
    {
        if (isset($_POST['org_id'])) {
            $org_id = preg_replace('[^0-9]', '', $_POST['org_id']);
            $query = $this->db->select(['logo', 'splash_background_color', 'branches_background_color', 'branches_title_color', 'branches_text_color',
                'login_background_image', 'login_background_color', 'login_settings_icon', 'login_text_color', 'login_phone_icon', 'login_color_button', 'welcome_background_color'
                , 'welcome_title_color', 'welcome_text_color', 'thanks_background_color', 'thanks_text_color', 'settings_background_color', 'settings_title_color', 'settings_text_color',
                'settings_button_text_color', 'settings_background_button_color', 'feedback_background_image', 'feedback_title_background_color', 'feedback_title_color', 'feedback_headers_background_color'
                , 'feedback_headers_color', 'feedback_dropdown_background_color', 'feedback_dropdown_text_color', 'feedback_rating_color', 'feedback_acceptable_image', 'feedback_acceptable_selected_image'
                , 'feedback_good_image', 'feedback_good_selected_image', 'feedback_excellent_image', 'feedback_excellent_selected_image', 'feedback_rating_text_color', 'feedback_separator_color', 'feedback_bottom_button_color'
                , 'feedback_bottom_text_color', 'feedback_comment_background_image'])->get_where('organizations', array('id' => $org_id));
            $count = $query->num_rows();
            $org = $query->result_array()[0];
            if ($count > 0) {
                echo json_encode(["result" => "success", 'data' => $org]);
                exit;
            } else {
                echo '{"result" : "false"}';
                exit;
            }
        } else {
            echo '{"result" : "false"}';
            exit;
        }
    }
///////////////////////////////////////////////////////////////////////////////
//Webservices for registering device for branch
    public function registerdevice()
    {
        if (isset($_POST['branch_id']) && isset($_POST['device_id'])) {
            $branch_id = preg_replace('[^0-9]', '', $_POST['branch_id']);
            $device_id = preg_replace('[^A-Za-z0-9]', '', $_POST['device_id']);
            $this->db->select('device_id,branch_id');
            $this->db->from('branch_devices');
            $this->db->where('device_id', $device_id);
            $this->db->where('branch_id !=', $branch_id);
            $query = $this->db->get()->result_array();
            if ($query) {
                echo '{"result" : "false"}';
                exit;
            } else {
                $this->db->select('device_id,branch_id');
                $this->db->from('branch_devices');
                $this->db->where('device_id', $device_id);
                $this->db->where('branch_id', $branch_id);
                $query = $this->db->get()->result_array();
                if ($query) {
                    echo '{"result" : "true"}';
                    exit;
                } else {

                }
                $data = array(
                    'device_id' => $device_id,
                    'branch_id' => $branch_id
                );
                $this->db->insert('branch_devices', $data);
                echo '{"result" : "true"}';
                exit;
            }

        } else {
            echo '{"result" : "false"}';
            exit;
        }
    }

//Webservices for getting all branches assigned for an organization
    public function getBranchs()
    {
        if (isset($_POST['restaurant_id']) && is_numeric($_POST['restaurant_id'])) {
            $rest_id = preg_replace("/[^0-9\s]/", " ", $_POST['restaurant_id']);
            $query = $this->db->get_where('branches', array('organization_id' => $rest_id));
            $count = $query->num_rows();
            $branches = $query->result_array();
            if ($count > 0) {
                $i = 0;
                echo '{ "branches" :[';
                foreach ($branches as $branche) {
                    echo '{"id":"' . $branche['id'] . '","name":"' . $branche['bran_name'] . '"}';
                    if ($i < sizeof($branches) - 1) {
                        echo ",";
                    }
                    $i++;
                }
                echo ']}';
            } else {
                echo '{"result" : "false"}';
                exit;
            }

        } else {
            echo '{"result" : "false"}';
            exit;
        }
    }

//Webservices For getting all suggested branches for an organization
    public function nextbran()
    {
        if (isset($_POST['restaurant_id']) && is_numeric($_POST['restaurant_id'])) {
            $rest_id = preg_replace("/[^0-9\s]/", " ", $_POST['restaurant_id']);
            $query = $this->db->get_where('suggestedBranches', array('organization_id' => $rest_id));
            $count = $query->num_rows();
            $branches = $query->result_array();
            if ($count > 0) {
                $i = 0;
                echo '{ "branches" :[';
                foreach ($branches as $branche) {
                    echo '{"id":"' . $branche['id'] . '","name":"' . $branche['branch'] . '"}';
                    if ($i < sizeof($branches) - 1) {
                        echo ",";
                    }
                    $i++;
                }
                echo ']}';
            } else {
                echo '{"result" : "false"}';
                exit;
            }
        } else {
            echo '{"result" : "false"}';
            exit;
        }
    }

//Webservices for getting all employees for an organization
    public function loademp()
    {

        if (isset($_POST['restaurant_id']) && is_numeric($_POST['restaurant_id'])
            && isset($_POST['branch_id']) && is_numeric($_POST['branch_id'])) {
            $rest_id = preg_replace("/[^0-9\s]/", " ", $_POST['restaurant_id']);
            $bran_id = preg_replace("/[^0-9\s]/", " ", $_POST['branch_id']);
            $sector = isset($_POST['sector']) ? $_POST['sector'] : 'Dine In';

            $query = $this->db->get_where('branchesToEmployes', array('branch_id' => $bran_id));
            $count = $query->num_rows();

            $employees = $query->result_array();
            if ($count > 0) {
                $employees = array_column($employees, 'employee_id');

                $this->db->select('organizationEmployees.id, employeName, logo,job_title,jobID');
                $this->db->from('organizationEmployees');
                $this->db->join('jobs', 'jobs.id = organizationEmployees.jobID', 'left');
                $this->db->where(array('organizationEmployees.type' => 'employee', 'organization_id' => $rest_id));

                if ($sector == 'Dine In') {
                    $this->db->where('organizationEmployees.jobID', 4);
                } elseif ($sector == 'Delivery') {
                    $this->db->where('organizationEmployees.jobID', 31);
                }

                $this->db->where_in('organizationEmployees.id', $employees);
                $query = $this->db->get();
                $count = $query->num_rows();
                $employees = $query->result_array();

                if ($count > 0) {
                    $i = 0;
                    echo '{ "employees" :[';
                    foreach ($employees as $employe) {
                        echo '{"employee_id":"' . $employe['id'] . '","employee_name":"' . $employe['employeName'] . '","image" : "http://svn.pitechnologies.net/aboshaara/assets/employees/' . $employe['logo'] . '","job": "' . $employe['job_title'] . '"}';
                        if ($i < sizeof($employees) - 1) {
                            echo ",";
                        }
                        $i++;
                    }
                    echo ']}';
                } else {
                    echo '{"result" : "false"}';
                    exit;
                }

            } else {
                echo '{"result" : "false"}';
                exit;
            }

        } else {
            echo '{"result" : "false"}';
            exit;
        }
    }

//Webservices for Getting all question of the organization
    public function loadquestion($sector = 4, $isOwner = null)
    {

        if (isset($_POST['restaurant_id']) && is_numeric($_POST['restaurant_id'])) {

            $rest_id = preg_replace("/[^0-9\s]/", " ", $_POST['restaurant_id']);
            $sector = (int)$sector;

            $sql = 'SELECT * FROM groups WHERE id IN (select DISTINCT(group_id) FROM questions where hidden in ("no","") AND organization_id=' . $rest_id . ') and sector=' . $sector . ' and cateringGuestFlag = ""';
            if (isset($isOwner)) {
                $sql = 'SELECT * FROM groups WHERE id IN (select DISTINCT(group_id) FROM questions where hidden in ("no","") AND organization_id=' . $rest_id . ') and sector=' . $sector . ' and cateringGuestFlag="yes"';
            }
            $query = $this->db->query($sql);
            $array = $query->result_array();
            if (!empty($array)) {
                echo '{"questions" : [';
                $idex = 0;
                foreach ($array as $grp) {
                    $type = ($grp['type'] == "MCQ") ? '1' : '2';
                    $quest = $this->db->get_where('questions', array('hidden' => 'no', 'group_id' => $grp['id'], 'organization_id' => $rest_id,'deleted !=' => 1));
                    $count = $quest->num_rows();
                    $allques = $quest->result_array();
                    if ($count > 0) {
                        echo '{"group_id" : "' . $grp['id'] . '","group_name":"' . $grp['group_alias'] . '","qroup_type" : "' . $type . '"';
                        echo ',"ques" : [';
                        $i = 0;
                        foreach ($allques as $que) {
                            echo '{"question_id":"' . $que['id'] . '","question_description":"' . $que['quesition'] . '"}';
                            if ($i < sizeof($allques) - 1) {
                                echo ',';
                            }
                            $i++;
                        }
                        echo "]}";
                    }
                    if ($idex < sizeof($array) - 1 && $count > 0) {
                        echo ',';
                    }
                    $idex++;
                }
                echo "]}";
            } else {
                echo '{"result" : "false"}';
                exit;
            }

        } else {
            echo '{"result" : "false"}';
            exit;
        }
    }

//wepservice for login user
    public function login()
    {
        if (isset($_POST['user_phone']) && is_numeric($_POST['user_phone'])
            && isset($_POST['restaurant_id']) && is_numeric($_POST['restaurant_id'])) {
            $phone = preg_replace("/[^0-9\s]/", " ", $_POST['user_phone']);
            $rest_id = preg_replace("/[^0-9\s]/", " ", $_POST['restaurant_id']);

            $this->db->where(array('phone' => $phone, 'organization_id' => $rest_id));
            $q = $this->db->get('guests_accounts');
            if (!$q->num_rows()) {
                echo '{"result" : "false"}';
                exit;
            } else {
                $data = $q->result_array();
                echo '{"result" : "true", "name":"' . $data[0]['name'] . '","user_id":"' . $data[0]['id'] . '","restaurant_id" : "' . $data[0]['organization_id'] . '"}';
            }
        } else {
            echo '{"result" : "false"}';
            exit;
        }
    }


//Webservices for registring a user
    public function signup()
    {
        if (isset($_POST['user_phone']) && isset($_POST['user_name']) &&
            isset($_POST['gender']) && isset($_POST['birthdate']) && isset($_POST['email'])
            && isset($_POST['restaurant_id'])) {
            $data['phone'] = preg_replace("/[^0-9\s]/", " ", $_POST['user_phone']);
            $data['organization_id'] = preg_replace("/[^0-9\s]/", " ", $_POST['restaurant_id']);
            $data['name'] = preg_replace("/[^a-zA-Z\s]/", " ", $_POST['user_name']);
            $data['gender'] = preg_replace("/[^a-zA-Z\s]/", " ", $_POST['gender']);
            $data['birthdate'] = preg_replace("/[^0-9\s\-]/", " ", $_POST['birthdate']);
            $data['email'] = preg_replace("/[^a-zA-Z0-9\s\.\@\_\-]/", " ", $_POST['email']);

            $this->db->where(array('phone' => $data['phone'], 'organization_id' => $data['organization_id']));
            $query = $this->db->get('guests_accounts');
            if ($query->num_rows() > 0) {
                echo '{"result" : "false"}';
                exit;
            }

            $this->db->where(array('email' => $data['email'], 'organization_id' => $data['organization_id']));
            $query = $this->db->get('guests_accounts');
            if ($query->num_rows() > 0) {
                echo '{"result" : "false"}';
                exit;
            }

            $this->db->insert('guests_accounts', $data);
            if ($this->db->affected_rows() != 1) {
                echo '{"result" : "false"}';
            } else {
                echo '{"result" : "true", "user_id":"' . $this->db->insert_id() . '", "restaurant_id":"' . $data['organization_id'] . '"}';
            }
        } else {
            echo '{"result" : "false"}';
            exit;
        }
    }


//wepservice for saving User feedback
    public function savereview()
    {
        if (isset($_POST['phone']) && isset($_POST['branch_id']) && isset($_POST['restaurant_id'])
            && (isset($_POST['questions']) || isset($_POST['next_branch_id']))) {
            $data1['phone'] = preg_replace("/[^0-9\s]/", " ", $_POST['phone']);
            $data['branch_id'] = preg_replace("/[^0-9\s]/", " ", $_POST['branch_id']);
            $organization_id = preg_replace("/[^0-9\s]/", " ", $_POST['restaurant_id']);

            $data['sector'] = isset($_POST['sector']) ? $_POST['sector'] : 'Dine In';

            $this->db->where(array('phone' => $data1['phone'], 'organization_id' => $organization_id));
            $q = $this->db->get('guests_accounts');
            if ($q->num_rows() < 1) {
                echo '{"result" : "false"}';
                exit;
            }

            $this->db->where(array('id' => $data['branch_id'], 'organization_id' => $organization_id));
            $b = $this->db->get('branches');
            if ($b->num_rows() < 1) {
                echo '{"result" : "false"}';
                exit;
            }

            $data['guest_id'] = $q->result_array()[0]['id'];
            $answers = json_decode($_POST['questions'], true);

            if (isset($_POST['next_branch_id'])) {
                $this->db->where(array('id' => $_POST['next_branch_id'], 'organization_id' => $organization_id));
                $nextbranch = $this->db->get('suggestedBranches');
                if ($nextbranch->num_rows() < 1) {
                    echo '{"result" : "false"}';
                    exit;
                }
                $data['sugg_branch_id'] = $_POST['next_branch_id'];
            }


            $this->db->insert('reviews', $data);
            if ($this->db->affected_rows() < 1) {
                echo '{"result" : "false"}';
            } else {
                $review_id = $this->db->insert_id();
                foreach ($answers['questions'] as $answer) {
                    $review_data['review_id'] = $review_id;
                    $review_data['group_id'] = $answer['group_id'];

                    $this->db->where(array('id' => $review_data['group_id']));
                    $groupdata = $this->db->get('groups');
                    if ($groupdata->num_rows() < 1) {
                        echo '{"result" : "false"}';
                        $this->db->delete('reviews', array('id' => $review_id));
                        exit;
                    }

                    $review_data['group_type'] = $answer['group_type'];

                    if (isset($answer['employee_id']) && ($review_data['group_type'] == 2)) {
                        $review_data['empl_id'] = $answer['employee_id'];
                        $this->db->where(array('id' => $review_data['empl_id'], 'organization_id' => $organization_id));
                        $empldata = $this->db->get('organizationEmployees');
                        if ($empldata->num_rows() < 1) {
                            echo '{"result" : "false"}';
                            $this->db->delete('reviews', array('id' => $review_id));
                            exit;
                        }
                    } else {
                        $review_data['empl_id'] = null;
                    }
                    if (!isset($answer['employee_id']) && $review_data['group_type'] == 2) {
                        echo '{"result" : "false"}';
                        $this->db->delete('reviews', array('id' => $review_id));
                        exit;
                    }
                    if (isset($answer['comment'])) {
                        $review_data['group_comment'] = $answer['comment'];
                    }
                    foreach ($answer['ques'] as $question) {
                        $review_data['question_id'] = $question['question_id'];
                        $review_data['result'] = $question['result'];

                        $this->db->where(array('id' => $review_data['question_id'], 'group_id' => $review_data['group_id']));
                        $questiondata = $this->db->get('questions');
                        if ($questiondata->num_rows() < 1) {
                            echo '{"result" : "false"}';
                            $this->db->delete('reviews', array('id' => $review_id));
                            exit;
                        }

                        if ($question['result'] == '1') {
                            $this->db->select('email');
                            $this->db->from('organizations');
                            $this->db->where('id', $organization_id);
                            $query = $this->db->get()->result_array();
                            $to = $query[0]['email'];

                            $this->db->select('email');
                            $this->db->distinct();
                            $this->db->from('organizationEmployees');
                            $this->db->join('jobs', 'jobID=jobs.id', 'left');
                            $this->db->join('branchesToEmployes', 'organizationEmployees.id =branchesToEmployes.employee_id', 'left');
                            $this->db->where('jobs.reveive_mails', 'yes');
                            $this->db->where('branchesToEmployes.branch_id', $data['branch_id']);
                            $query = $this->db->get()->result_array();
                            $cc = array();
                            foreach ($query as $email) {
                                $cc[] = $email['email'];
                            }
                            $this->email->to($to);
                            $this->email->cc($cc);
                            $this->email->from('ifeedbackalert@pitechnologies.net', 'iFeedback System');
                            $this->email->subject('Infected Moment Of Truth Alert');
                            $message = "Branch:{$b->result_array()[0]['bran_name']}<br/>Customer Name:{$q->result_array()[0]['name']}
							<br/>Phone number:{$data1['phone']}<br/>E-mail:{$q->result_array()[0]['email']}<br/>Infected Group:{$groupdata->result_array()[0]['group_alias']}<br/>
							Infected Question:{$questiondata->result_array()[0]['quesition']}";
                            if ($review_data['group_type'] == 2) {
                                $message .= "<br/>Waiter Name:{$empldata->result_array()[0]['employeName']}";
                            }
                            $this->email->message($message);
                            $this->email->send();
                        }
                        $this->db->insert('review_data', $review_data);
                    }

                }
                echo '{"result" : "true"}';
            }
        } else {
            echo '{"result" : "false"}';
            exit;
        }
    }
}
