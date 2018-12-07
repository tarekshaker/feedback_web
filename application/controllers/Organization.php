<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Organization extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Organization_model');
        $this->load->library('grocery_CRUD');
        $this->load->helper('security');

        $this->grocery_crud->set_theme('datatables');
        $function = $this->uri->segment('2');
        if ($this->session->userdata('user_logged_in') !== true &&
            strtolower($function) != 'login') {
            redirect('organization/login');
        } else if ($this->session->userdata('user_logged_in') === true &&
            strtolower($function) == 'login') {
            redirect('organization/dashboard');
        }
    }
    /*****************************************************************************/
    /**
     * index Function
     * @return mixed
     */
    /*****************************************************************************/
    public function index()
    {
        redirect('organization/dashboard');
    }

    /*****************************************************************************/
    public function employeeSectionQuestions($id, $group)
    {
        if (isset($id) && isset($group)) {
            if (!is_int($id) && !is_int($group)) {

                $query = array();
                $data = array();
                $branches = array();
                if ($this->session->userdata('empl_id')) {
                    $this->db->select('branch_id');
                    $this->db->from('branchesToEmployes');
                    $this->db->where('employee_id', $this->session->userdata('empl_id'));
                    $query = $this->db->get()->result_array();
                    $branches = array_column($query, 'branch_id');
                } else {
                    $this->db->select('id');
                    $this->db->from('branches');
                    $this->db->where('organization_id', $this->session->userdata('user_id'));
                    $query = $this->db->get()->result_array();
                    $branches = array_column($query, 'id');
                }
                $this->db->select('id,bran_name');
                $this->db->from('branches');
                $this->db->where_in('id', $branches);
                $query = $this->db->get()->result_array();
                $data['branches'] = $query;
                $this->db->select('id,job_title');
                $this->db->from('jobs');
                $query = $this->db->get()->result_array();
                $data['jobs'] = $query;
                $this->db->select('id,group_name');
                $this->db->from('groups');
                $query = $this->db->get()->result_array();
                $data['groups'] = $query;
                $branches = implode(',', $branches);
                if (isset($_POST['branches'])) {
                    $branches = implode(',', $_POST['branches']);
                }
                $mainQuery = "SELECT organizationEmployees.id,organizationEmployees.employeName,jobs.job_title,branches.bran_name,
					branches.id As bran_id FROM organizationEmployees LEFT join jobs on organizationEmployees.jobID = jobs.id
					LEFT JOIN branchesToEmployes on organizationEmployees.id = branchesToEmployes.employee_id LEFT JOIN
					branches on branchesToEmployes.branch_id = branches.id where branches.id in ($branches) and organizationEmployees.id = $id";
                if (isset($_POST['jobs'])) {
                    $jobs = implode(',', $_POST['jobs']);
                    $mainQuery .= " and organizationEmployees.jobID in ($jobs)";
                }
                $query = $this->db->query($mainQuery);
                $result = $query->result();
                if ($result) {
                    foreach ($result as &$row) {
                        $resultQuery = "SELECT round(((sum((case when (`t2`.`result` = 2) then (`t2`.`count` * 0.5) when (`t2`.`result` = 1) then (`t2`.`count` * 0) else `t2`.`count` end)) / sum(`t2`.`count`)) * 100),2) AS `all_reviews` from (SELECT result,empl_id, count(result) as count FROM `review_data` LEFT join reviews on reviews.id = review_data.review_id where empl_id is Not Null and empl_id = {$row->id} ";
                        if (isset($_POST['groups'])) {
                            $groups = implode(',', $_POST['groups']);
                            $resultQuery .= " and review_data.group_id in ($groups)";
                        }
                        if (isset($_POST['daterange'])) {
                            $dates = explode('-', $_POST['daterange']);
                            $dates[0] = date('Y-m-d', strtotime($dates[0])) . " 00:00:00";
                            $dates[1] = date('Y-m-d', strtotime($dates[1])) . " 23:59:59";
                            $resultQuery .= " and reviews.review_time BETWEEN '{$dates[0]}' AND '{$dates[1]}' ";
                        }
                        $resultQuery .= "group by result,empl_id ORDER by empl_id) as t2";
                        $query2 = $this->db->query($resultQuery);
                        $result2 = $query2->result();

                        $row->result = ($result2) ? $result2[0]->all_reviews . "%" : "0%";
                    }
                }
                $data['result'] = $result;

                $query = $this->db->query("SELECT id,group_name,round(((sum((case when (`t2`.`result` = 2) then (`t2`.`count` * 0.5) when (`t2`.`result` = 1) then
					(`t2`.`count` * 0) else `t2`.`count` end)) / sum(`t2`.`count`)) * 100),2) AS `all_reviews` from
					(select groups.id,groups.group_name,review_data.result,count(review_data.result) as count from groups
					left join review_data on review_data.group_id = groups.id WHERE review_data.empl_id = $id and groups.id=$group group by
					review_data.result,groups.id) as t2");
                $result = $query->result();

                $data['data'] = $result;


                $query = $this->db->query("SELECT id,quesition,round(((sum((case when (`t2`.`result` = 2) then
					(`t2`.`count` * 0.5) when (`t2`.`result` = 1) then (`t2`.`count` * 0) else `t2`.`count` end)) /
					sum(`t2`.`count`)) * 100),2) AS `all_reviews` from (select questions.id,questions.quesition,review_data.result,
						count(review_data.result) as count from questions left join review_data on review_data.question_id = questions.id
						WHERE review_data.empl_id = $id and review_data.group_id=$group group by review_data.result,questions.id) as t2");
                $result = $query->result();

                $data['question'] = $result;
                $this->admin_view('employeQuestions', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    public function employeeSectionGroups($id)
    {
        if (isset($id)) {
            if (!is_int($id)) {

                $query = array();
                $data = array();
                $branches = array();
                if ($this->session->userdata('empl_id')) {
                    $this->db->select('branch_id');
                    $this->db->from('branchesToEmployes');
                    $this->db->where('employee_id', $this->session->userdata('empl_id'));
                    $query = $this->db->get()->result_array();
                    $branches = array_column($query, 'branch_id');
                } else {
                    $this->db->select('id');
                    $this->db->from('branches');
                    $this->db->where('organization_id', $this->session->userdata('user_id'));
                    $query = $this->db->get()->result_array();
                    $branches = array_column($query, 'id');
                }
                $this->db->select('id,bran_name');
                $this->db->from('branches');
                $this->db->where_in('id', $branches);
                $query = $this->db->get()->result_array();
                $data['branches'] = $query;
                $this->db->select('id,job_title');
                $this->db->from('jobs');
                $query = $this->db->get()->result_array();
                $data['jobs'] = $query;
                $this->db->select('id,group_name');
                $this->db->from('groups');
                $query = $this->db->get()->result_array();
                $data['groups'] = $query;
                $branches = implode(',', $branches);
                if (isset($_POST['branches'])) {
                    $branches = implode(',', $_POST['branches']);
                }
                $mainQuery = "SELECT organizationEmployees.id,organizationEmployees.employeName,jobs.job_title,branches.bran_name,
					branches.id As bran_id FROM organizationEmployees LEFT join jobs on organizationEmployees.jobID = jobs.id
					LEFT JOIN branchesToEmployes on organizationEmployees.id = branchesToEmployes.employee_id LEFT JOIN
					branches on branchesToEmployes.branch_id = branches.id where branches.id in ($branches) and organizationEmployees.id = $id";
                if (isset($_POST['jobs'])) {
                    $jobs = implode(',', $_POST['jobs']);
                    $mainQuery .= " and organizationEmployees.jobID in ($jobs)";
                }
                $query = $this->db->query($mainQuery);
                $result = $query->result();
                if ($result) {
                    foreach ($result as &$row) {
                        $resultQuery = "SELECT round(((sum((case when (`t2`.`result` = 2) then (`t2`.`count` * 0.5) when (`t2`.`result` = 1) then (`t2`.`count` * 0) else `t2`.`count` end)) / sum(`t2`.`count`)) * 100),2) AS `all_reviews` from (SELECT result,empl_id, count(result) as count FROM `review_data` LEFT join reviews on reviews.id = review_data.review_id where empl_id is Not Null and empl_id = {$row->id} ";
                        if (isset($_POST['groups'])) {
                            $groups = implode(',', $_POST['groups']);
                            $resultQuery .= " and review_data.group_id in ($groups)";
                        }
                        if (isset($_POST['daterange'])) {
                            $dates = explode('-', $_POST['daterange']);
                            $dates[0] = date('Y-m-d', strtotime($dates[0])) . " 00:00:00";
                            $dates[1] = date('Y-m-d', strtotime($dates[1])) . " 23:59:59";
                            $resultQuery .= " and reviews.review_time BETWEEN '{$dates[0]}' AND '{$dates[1]}' ";
                        }
                        $resultQuery .= "group by result,empl_id ORDER by empl_id) as t2";
                        $query2 = $this->db->query($resultQuery);
                        $result2 = $query2->result();

                        $row->result = ($result2) ? $result2[0]->all_reviews . "%" : "0%";
                    }
                }
                $data['result'] = $result;

                $query = $this->db->query("SELECT id,group_name,round(((sum((case when (`t2`.`result` = 2) then (`t2`.`count` * 0.5) when (`t2`.`result` = 1) then
					(`t2`.`count` * 0) else `t2`.`count` end)) / sum(`t2`.`count`)) * 100),2) AS `all_reviews` from
					(select groups.id,groups.group_name,review_data.result,count(review_data.result) as count from groups
					left join review_data on review_data.group_id = groups.id WHERE review_data.empl_id = $id group by
					review_data.result,groups.id) as t2");
                $result = $query->result();

                $data['data'] = $result;

                $this->admin_view('employeeGroup', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    public function employeeSection()
    {
        $query = array();
        $branches = array();

        if ($this->session->userdata('empl_id')) {
            $admin_user = $this->Organization_model->checkAdmin($this->session->userdata('empl_id'));

            if ($admin_user['type'] != 'admin') {
                $userPermission = $this->Organization_model->checkPermission($this->session->userdata('empl_id'), 'employeeReport');
                if ((!$userPermission['employeeReport'])) {
                    show_404();
                }

            }
            $this->db->select('id,bran_name');
            $this->db->from('branchesToEmployes');
            $this->db->where('employee_id', $this->session->userdata('empl_id'));
            $this->db->join('branches','branchesToEmployes.branch_id = branches.id');
            $query = $this->db->get()->result_array();
            $data['branches'] = $query;
            $branches = array_column($query, 'id');

        } else {
            $this->db->select('id,bran_name');
            $this->db->from('branches');
            $this->db->where('organization_id', $this->session->userdata('user_id'));
            $query = $this->db->get()->result_array();
            $data['branches'] = $query;
            $branches = array_column($query, 'id');
        }
        $branchesIN = implode(',', $branches);

        $query = $this->db->get('groupsSectors');
        $result = $query->result_array();
        $data['sectors'] = $result;

        $query = $this->db->get('groups');
        $result = $query->result_array();
        $data['groups'] = $result;

        $this->db->select('distinct(job_title),jobs.id');
        $this->db->from('organizationEmployees');
        $this->db->join('jobs', 'organizationEmployees.jobID=jobs.id', 'left');
        $this->db->where('organization_id', $this->session->userdata('user_id'));
        $result = $this->db->get()->result_array();
        $data['jobs'] = $result;


        if (isset($_POST['branches'])) {
            $branchesIN = implode(',', $_POST['branches']);
        }


        $unique_id = ($this->session->userdata('empl_id')) ? $this->session->userdata('empl_id') : $this->session->userdata('user_id');


        $this->db->query("DROP TABLE IF EXISTS employeesReport$unique_id");

        $queryString = "CREATE TABLE employeesReport$unique_id 
                        As SELECT organizationEmployees.id,organizationEmployees.employeName As 'Employee Name',
                                  jobs.job_title As 'Job Title',branches.bran_name, branches.id As bran_id,jobs.sector As 'Sector'
                        FROM organizationEmployees
                        LEFT join jobs on organizationEmployees.jobID = jobs.id 
                        LEFT JOIN branchesToEmployes on organizationEmployees.id = branchesToEmployes.employee_id 
                        LEFT JOIN branches on branchesToEmployes.branch_id = branches.id 
                        where branches.id in ($branchesIN)";


        if (isset($_POST['sectors'])) {
            $sectorsIN = implode("', '", $_POST['sectors']);
            $queryString .= " and jobs.sector IN  ('" . $sectorsIN . "') ";
        }

        if (isset($_POST['jobs'])) {
            $jobsIn = implode(',', $_POST['jobs']);
            $queryString .= " and organizationEmployees.jobID in ($jobsIn)";
        }


        $this->db->query($queryString);

        $query = $this->db->query('SELECT id from employeesReport' . $unique_id);

        $result = $query->result();

        $this->db->query('ALTER TABLE employeesReport' . $unique_id . ' ADD COLUMN result varchar(255) DEFAULT 0');

        foreach ($result as &$row) {

            $resultQuery = "SELECT empl_id,round(((sum((case when (`t2`.`result` = 2) then (`t2`.`count` * 0.5) when (`t2`.`result` = 1) then (`t2`.`count` * 0) else `t2`.`count` end)) / sum(`t2`.`count`)) * 100),2) AS `all_reviews` from (SELECT result,empl_id, count(result) as count FROM `review_data` LEFT join reviews on reviews.id = review_data.review_id where empl_id is Not Null and empl_id = {$row->id} ";

            if (isset($_POST['groups'])) {
                $groups = implode(',', $_POST['groups']);
                $resultQuery .= " and review_data.group_id in ($groups)";
            }

            if (isset($_POST['daterange']) && $_POST['daterange'] != '') {
                $dates = explode('-', $_POST['daterange']);
                $dates[0] = date('Y-m-d', strtotime($dates[0])) . " 00:00:00";
                $dates[1] = date('Y-m-d', strtotime($dates[1])) . " 23:59:59";
                $resultQuery .= " and reviews.review_time BETWEEN '{$dates[0]}' AND '{$dates[1]}' ";
            }

            $resultQuery .= " group by result,empl_id ORDER by empl_id) as t2 group by empl_id";
            $query2 = $this->db->query($resultQuery);
            $result2 = $query2->result();
            foreach ($result2 as $row2) {
                $empl_result = $row2->all_reviews;
                $empl_id = $row2->empl_id;
                $resultQuery = "UPDATE employeesReport" . $unique_id . " set result = '$empl_result' where id = '$empl_id'";
                $this->db->query($resultQuery);
            }
        }


        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table("employeesReport$unique_id");
        $crud->set_primary_key('id');
        $crud->set_subject('Employees Report');
        $crud->fields('id', 'Employee Name', 'Job Title', 'Sector', '');
        $crud->columns('Employee Name', 'Job Title', 'Sector', 'result');
        $crud->callback_column('result', array($this, '_addpercent'));

        $crud->unset_add();
        $crud->unset_edit();
        $crud->unset_delete();
        $crud->unset_read();

        $data['page_title'] = "Employees Report";
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
    }
    /*****************************************************************************/

    /*****************************************************************************/
    /**
     * Dachboard function
     * @return mixed
     */
    /*****************************************************************************/
    public function dashboard()
    {
        $query = array();
        $branches = array();
        if ($this->session->userdata('empl_id')) {
            $this->db->select('branch_id');
            $this->db->from('branchesToEmployes');
            $this->db->where('employee_id', $this->session->userdata('empl_id'));
            $query = $this->db->get()->result_array();
            $branches = array_column($query, 'branch_id');
        } else {
            $this->db->select('id');
            $this->db->from('branches');
            $this->db->where('organization_id', $this->session->userdata('user_id'));
            $query = $this->db->get()->result_array();
            $branches = array_column($query, 'id');
        }

        //Getting the upper tile counters
        $this->db->select('result,COUNT(*) as count');
        $this->db->from('review_data');
        $this->db->join('reviews', 'reviews.id = review_data.review_id', 'left');
        $this->db->where_in('branch_id', $branches);
        $this->db->group_by("result");
        $this->db->order_by("count", "desc");
        $result = $this->db->get()->result_array();

        $data = array();
        $data['exc'] = 0;
        $data['good'] = 0;
        $data['accept'] = 0;
        if ($result) {
            foreach ($result as $res) {
                if ($res['result'] == 3) {
                    $data['exc'] = $res['count'];
                } else if ($res['result'] == 2) {
                    $data['good'] = $res['count'];
                } else if ($res['result'] == 1) {
                    $data['accept'] = $res['count'];
                }
            }
            $data['total'] = $data['exc'] + 0.5 * $data['good'];
        }
        //End Getting the upper tile counters

        //**getting branches
        $this->db->select('id,bran_name');
        $this->db->from('branches');
        $this->db->where_in('id', $branches);
        $query = $this->db->get()->result_array();
        $data['branches'] = $query;

        //**getting sectors
        $this->db->select('organizations.id, online_service.service,inhouse_service.service As serv');
        $this->db->from('organizations');
        $this->db->join('organizationToOnline', ' organizationToOnline.organization_id= organizations.id', 'left');
        $this->db->join('online_service', 'online_service.id = organizationToOnline.serviceID', 'left');
        $this->db->join('organizationToInhouse', 'organizationToInhouse.organization_id=organizations.id', 'left');
        $this->db->join('inhouse_service', 'inhouse_service.id=organizationToInhouse.serviceID', 'left');
        $this->db->where('organizations.id', $this->session->userdata('user_id'));
        $result = $this->db->get()->result_array();

        $data['sectors'] = array_values(array_unique(array_column($result, 'service')));
        $data['sectors'] = array_merge($data['sectors'], array_values(array_unique(array_column($result, 'serv'))));

        $this->admin_view('dashboard', $data);
    }

    //function for getting charts Data
    public function getBranchchart($branch_id)
    {
        $this->db->select('result,COUNT(*) as count,groupsSectors.Sector');
        $this->db->from('review_data');
        $this->db->join('reviews', 'reviews.id = review_data.review_id', 'left');
        $this->db->join('groups', 'groups.id = review_data.group_id', 'left');
        $this->db->join('groupsSectors', 'groupsSectors.id = groups.sector', 'left');
        $this->db->where_in('branch_id', $branch_id);
        $this->db->group_by("result,Sector");
        $this->db->order_by("Sector", "desc");
        $this->db->order_by("result", "desc");

        $result = $this->db->get()->result_array();

        $data = array();

        $data['sectors'] = array_values(array_unique(array_column($result, 'Sector')));

        $data['Excellect'] = array_fill(0, sizeof($data['sectors']), 0);
        $data['Good'] = array_fill(0, sizeof($data['sectors']), 0);
        $data['Poor'] = array_fill(0, sizeof($data['sectors']), 0);
        for ($i = 0; $i < sizeof($data['sectors']); $i++) {
            foreach ($result as $res) {
                if ($data['sectors'][$i] == $res['Sector']) {
                    if ($res['result'] == 3) {
                        $data['Excellect'][$i] = $res['count'];
                    } else if ($res['result'] == 2) {
                        $data['Good'][$i] = $res['count'];
                    } else if ($res['result'] == 1) {
                        $data['Poor'][$i] = $res['count'];
                    }
                }
            }
            $data['donut'][] = array('value' => $data['Excellect'][$i] + 0.5 * $data['Good'][$i], 'name' => $data['sectors'][$i]);
        }

        echo json_encode($data);
    }
    /*****************************************************************************/
    /**
     * Second Dash - filtered Dash
     * @return mixed
     */
    /*****************************************************************************/
    public function filteredDash()
    {
        if (!isset($_POST['to'], $_POST['from'], $_POST['sector'])) {
            show_404();

        }
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_POST['to'])
            && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_POST['from'])
            && preg_match("/^[a-zA-Z -]+$/", $_POST['sector'])) {
        } else {
            show_404();
        }
        $query = array();
        $branches = array();
        if ($this->session->userdata('empl_id')) {
            $this->db->select('branch_id');
            $this->db->from('branchesToEmployes');
            $this->db->where('employee_id', $this->session->userdata('empl_id'));
            $query = $this->db->get()->result_array();
            $branches = array_column($query, 'branch_id');
        } else {
            $this->db->select('id');
            $this->db->from('branches');
            $this->db->where('organization_id', $this->session->userdata('user_id'));
            $query = $this->db->get()->result_array();
            $branches = array_column($query, 'id');
        }


        //Getting the upper tile counters
        $this->db->select('result,COUNT(*) as count');
        $this->db->from('review_data');
        $this->db->join('reviews', 'reviews.id = review_data.review_id', 'left');
        $this->db->join('groups', 'groups.id = group_id', 'left');
        $this->db->join('groupsSectors', 'groupsSectors.id = groups.sector', 'left');
        $this->db->where_in('branch_id', $branches);
        if ($_POST['sector'] == 'Delivery') {
            $this->db->where('groups.sector', 1);
        } else if ($_POST['sector'] == 'Catering') {
            $this->db->where('groups.sector', 2);
        } else if ($_POST['sector'] == 'Fast Food') {
            $this->db->where('groups.sector', 3);
        } else if ($_POST['sector'] == 'Dine In') {
            $this->db->where('groups.sector', 4);
        }
        $this->db->where('reviews.review_time BETWEEN "' . $_POST['from'] . '" and "' . $_POST['to'] . ' 23:59:59' . '"');

        $this->db->group_by("result");
        $this->db->order_by("count", "desc");
        $result = $this->db->get()->result_array();
//        echo $this->db->last_query();die();

        $data = array();
        $data['exc'] = 0;
        $data['good'] = 0;
        $data['accept'] = 0;
        if ($result) {
            foreach ($result as $res) {
                if ($res['result'] == 3) {
                    $data['exc'] = $res['count'];
                } else if ($res['result'] == 2) {
                    $data['good'] = $res['count'];
                } else if ($res['result'] == 1) {
                    $data['accept'] = $res['count'];
                }
            }
            $data['total'] = $data['exc'] + 0.5 * $data['good'];
        }
        //Getting the upper tile counters

        //**getting sectors
        $this->db->select('organizations.id, online_service.service,inhouse_service.service As serv');
        $this->db->from('organizations');
        $this->db->join('organizationToOnline', ' organizationToOnline.organization_id= organizations.id', 'left');
        $this->db->join('online_service', 'online_service.id = organizationToOnline.serviceID', 'left');
        $this->db->join('organizationToInhouse', 'organizationToInhouse.organization_id=organizations.id', 'left');
        $this->db->join('inhouse_service', 'inhouse_service.id=organizationToInhouse.serviceID', 'left');
        $this->db->where('organizations.id', $this->session->userdata('user_id'));
        $result = $this->db->get()->result_array();
        $data['sectors'] = array_values(array_unique(array_column($result, 'service')));
        $data['sectors'] = array_merge($data['sectors'], array_values(array_unique(array_column($result, 'serv'))));
        //**getting sectors

//guage chart for total feedbacks
        $this->db->select('count(id) As count');
        $this->db->from('reviews');
        $this->db->where_in('branch_id', $branches);
        $this->db->where('reviews.review_time BETWEEN "' . $_POST['from'] . '" and "' . $_POST['to'] . ' 23:59:59' . '"');
//        $this->db->where('reviews.sector = "' . $_POST['sector'] . '"');
        $result = $this->db->get()->result_array();

        $data['totalFeedbacks'] = $result[0]['count'];

        $this->db->select('count(id) As count');
        $this->db->from('reviews');
        $this->db->where_in('branch_id', $branches);
        $this->db->where('reviews.review_time BETWEEN "' . $_POST['from'] . '" and "' . $_POST['to'] . ' 23:59:59' . '"');
        $this->db->where('reviews.sector = "' . $_POST['sector'] . '"');
        $result = $this->db->get()->result_array();

        $data['sectorFeedback'] = $result[0]['count'];

//        print_r($data);die();

//        print_r($data);die();

        //guage chart for total feedbacks

        //circle chart for total feedbacks for each branch
        $this->db->select('branch_id,branches.bran_name, count(*) As count');
        $this->db->from('reviews');
        $this->db->join('branches', 'branches.id = branch_id ', 'left');
        $this->db->where_in('branch_id', $branches);
        $this->db->where('reviews.review_time BETWEEN "' . $_POST['from'] . '" and "' . $_POST['to'] . ' 23:59:59' . '"');
        $this->db->where('reviews.sector = "' . $_POST['sector'] . '"');
        $this->db->group_by("branch_id");
        $this->db->order_by("count");
        $result = $this->db->get()->result_array();
        $data['donutBranches'] = array_column($result, 'bran_name');
        foreach ($result as $branc) {
            $data['donut'][] = array('value' => $branc['count'], 'name' => $branc['bran_name']);
        }

        //circle chart for total feedbacks for each branch

        //bar chart for all branches feedbackes result
        $this->db->select('result,branches.bran_name,count(result) as count');
        $this->db->from('review_data');
        $this->db->join('reviews', 'reviews.id = review_data.review_id ', 'left');
        $this->db->join('branches', 'branches.id = branch_id ', 'left');
        $this->db->where_in('branch_id', $branches);
        $this->db->where('reviews.review_time BETWEEN "' . $_POST['from'] . '" and "' . $_POST['to'] . ' 23:59:59' . '"');
        $this->db->where('reviews.sector = "' . $_POST['sector'] . '"');
        $this->db->group_by("result,branches.bran_name");
        $subQuery = $this->db->get_compiled_select();
        $this->db->select('bran_name,ROUND( (sum(CASE WHEN result = 2 THEN count*0.5 WHEN result = 1 THEN count*0 ELSE count END)/sum(count))*100,2) as percent ');
        $this->db->from("($subQuery) as branchesfeedbacks");
        $this->db->group_by('bran_name');
        $result = $this->db->get()->result_array();
        $data['barchart'] = $result;


        //bar chart for all branches feedbackes result

        //percentage chart for all branches
        $this->db->select('reviews.branch_id,result,COUNT(*) as count');
        $this->db->from('review_data');
        $this->db->join('reviews', 'reviews.id = review_data.review_id', 'left');
        $this->db->join('groups', 'groups.id = group_id', 'left');
        $this->db->where_in('branch_id', $branches);
        if ($_POST['sector'] == 'Delivery') {
            $this->db->where('groups.sector', 1);
        } else if ($_POST['sector'] == 'Catering') {
            $this->db->where('groups.sector', 2);
        } else if ($_POST['sector'] == 'Fast Food') {
            $this->db->where('groups.sector', 3);
        } else if ($_POST['sector'] == 'Dine In') {
            $this->db->where('groups.sector', 4);
        }
        $this->db->where('reviews.review_time BETWEEN "' . $_POST['from'] . '" and "' . $_POST['to'] . ' 23:59:59' . '"');
        $this->db->group_by("result,reviews.branch_id");
        $this->db->order_by("count", "desc");
        $result = $this->db->get()->result_array();
        $branches_initialization = array_column($result, 'branch_id');
        for ($i = 0; $i < sizeof($branches_initialization); $i++) {
            $data['branchPercent'][$branches_initialization[$i]] = array(0 => array('value' => 0, 'name' => 'Excellent'), 1 => array('value' => 0, 'name' => 'Good'), 2 => array('value' => 0, 'name' => 'Poor'));
        }
        foreach ($result as $branch) {
            if ($branch['result'] == 3) {
                $data['branchPercent'][$branch['branch_id']][0]['value'] = $branch['count'];
            } else if ($branch['result'] == 2) {
                $data['branchPercent'][$branch['branch_id']][1]['value'] = $branch['count'];
            } else if ($branch['result'] == 1) {
                $data['branchPercent'][$branch['branch_id']][2]['value'] = $branch['count'];
            }
        }
        //percentage chart for all branches

        //comparing branches bar chart
        $this->db->select('bran_name,group_name,round(avg(percent),2) as percent');
        $this->db->from('comparingBranchesView');
        $this->db->where('sector', $_POST['sector']);
        $this->db->where_in('branch_id', $branches);
        $this->db->where('review_time BETWEEN "' . $_POST['from'] . '" and "' . $_POST['to'] . ' 23:59:59' . '"');
        $this->db->group_by("bran_name,group_name");
        $query = $this->db->get()->result_array();

        $data['comparingchartPercent'] = array();
        if ($query) {
            $data['groupname'] = array_values(array_unique(array_column($query, 'group_name')));
            $data['branchnames'] = array_values(array_unique(array_column($query, 'bran_name')));


            for ($i = 0; $i < sizeof($data['branchnames']); $i++) {
                $data['comparingchartPercent'][] = array_fill(0, sizeof($data['groupname']), 0);
            }
            foreach ($query as $row) {
                $rowindex = array_search($row['bran_name'], $data['branchnames']);
                $colindex = array_search($row['group_name'], $data['groupname']);
                $data['comparingchartPercent'][$rowindex][$colindex] = $row['percent'];
            }
        }
        //comparing branches bar chart

        //**getting branches
        $this->db->select('id,bran_name');
        $this->db->from('branches');
        $this->db->where_in('id', $branches);
        $query = $this->db->get()->result_array();
        $data['branches'] = $query;
        $this->admin_view('filteredDash', $data);
    }

    /*****************************************************************************/
    /**
     * Load the view
     * @return mixed
     */
    /*****************************************************************************/
    private function admin_view($view = null, $data = null)
    {
        $this->load->view('admin/includes/user_header', $data);
        $this->load->view('admin/' . $view, $data);
        $this->load->view('admin/includes/footer', $data);
    }
    /*****************************************************************************/
    /**
     * Login Function
     * @return mixed
     */
    /*****************************************************************************/
    public function login()
    {
        $data['auth_error'] = '';
        if ($this->input->post('admin_login')) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'required');
            $this->form_validation->set_rules('position', 'Position', 'required');
            if ($this->form_validation->run() !== FALSE) {
                $email = preg_replace('/\s+/', '', $this->input->post('email'));
                $password = trim($this->input->post('password'));
                $userdata = array();
                if ($this->input->post('position') == 'employee') {
                    $userdata = $this->Organization_model->authenticate($email, $password, 'organizationEmployees');
                } else if ($this->input->post('position') == 'organization') {
                    $userdata = $this->Organization_model->authenticate($email, $password, 'organizations');
                } else {
                    show_404();
                }
                if ($userdata === false) {
                    $data['auth_error'] = 'Wrong email or password';
                } else {
                    $today = date("Y-m-d H:i:s");
                    $diff = strtotime($userdata['DayToExpire']) - strtotime($today);
                    if (strtotime($userdata['DayToExpire']) < strtotime($today)) {
                        $data['auth_error'] = 'Your Organization account expired. Please contact the administrator';
                    } else {
                        if ($diff <= 2687992) {
                            $expiremsg = "Please Contact the administrator due to account expiration period less than one month. it will expire at " . $userdata['DayToExpire'];
                            $this->session->set_userdata(array('expiremsg' => $expiremsg));
                        }
                        $this->session->set_userdata(array('user_logo' => $userdata['logo'],
                            'user_email' => $userdata['email'], 'user_full_name' => $userdata['organization_name'],
                            'user_logged_in' => true));
                        if ($this->input->post('position') == 'employee') {
                            $this->session->set_userdata(array(
                                'user_full_name' => $userdata['employeName'],
                                'user_id' => $userdata['organization_id'],
                                'empl_id' => $userdata['id'],
                                'questionSection' => $userdata['questionSection'],
                                'suggestedBranchSection' => $userdata['suggestedBranchSection'],
                                'screenSaverSection' => $userdata['screenSaverSection'],
                                'employeeSection' => $userdata['employeeSection'],
                                'customerBehaviourSection' => $userdata['customerBehaviourSection'],
                                'reportSection' => $userdata['reportSection'],
                                'employeeReport' => $userdata['employeeReport'],
                                'birthdateSection' => $userdata['birthdateSection'],
                                'suggestedBranchReport' => $userdata['suggestedBranchReport'],
                                'reviewsComments' => $userdata['reviewsComments'],
                                'backLogSection' => $userdata['backLogSection'],
                                'webviewSection' => $userdata['webviewSection']
                            ));
                        } else {
                            $this->session->set_userdata(array('user_full_name' => $userdata['organization_name'],
                                'user_id' => $userdata['id']));
                        }
                        $this->session->set_flashdata('notification_success',
                            "You have successfully logged in.");
                        redirect('organization/dashboard', 'refresh');
                    }
                }
            }
        }
        $this->load->view('admin/user_login', $data);
    }
    /*****************************************************************************/
    /**
     * Logout and unset all session variables
     * @return mixed
     */
    /*****************************************************************************/
    public function logout()
    {
        if ($this->session->userdata('user_logged_in') === true) {
            $this->session->sess_destroy();
            $this->session->unset_userdata(array('user_logo', 'user_logged_in', 'user_email',
                'user_full_name', 'empl_id', 'user_id', 'expiremsg'));
            redirect('organization/login');
        } else {
            redirect('404');
        }
    }
    /*****************************************************************************/
    /**
     * ScreenSaver module edit add delete
     * @return mixed
     */
    /*****************************************************************************/
    function screenSaverSection()
    {
        $userPermission = array('screenSaverSection' => null);
        if ($this->session->userdata('empl_id')) {
            $admin_user = $this->Organization_model->checkAdmin($this->session->userdata('empl_id'));
            if ($admin_user['type'] != 'admin') {
                $userPermission = $this->Organization_model->checkPermission($this->session->userdata('empl_id'), 'screenSaverSection');
                if ((!$userPermission['screenSaverSection'])) {
                    show_404();
                }
            }

        }
        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table('screen_server');
        $crud->set_subject('Screen Saver');
        $crud->fields('image', 'organization_id');
        $crud->display_as('image', 'Screensaver Image');
        $crud->required_fields('image');
        $crud->columns('image');
        $crud->change_field_type('organization_id', 'hidden');
        $crud->set_field_upload('image', 'assets/screensaver');
        $crud->callback_before_insert(array($this, 'id_field_add'));
        if (!empty($userPermission)) {
            if ($userPermission['screenSaverSection'] == 'view') {
                $crud->unset_add();
                $crud->unset_edit();
                $crud->unset_delete();
            }
        }

        //callback for upload valid image
        $crud->callback_before_upload(array($this, '_callback_check_image'));

        $crud->unset_read();
        $crud->where('organization_id', $this->session->userdata('user_id'));


        $crud->callback_after_insert(array($this, 'log_user_after_insert'));
        $crud->callback_after_update(array($this, 'log_user_after_update'));
        $crud->callback_after_delete(array($this, 'user_after_delete'));

        $data['page_title'] = "Screen Saver";
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
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

    function id_field_add($post)
    {
        $post['organization_id'] = $this->session->userdata('user_id');
        return $post;
    }
    /*****************************************************************************/
    /**
     * Viewing Organization Branches
     * @return mixed
     */
    /*****************************************************************************/
    public function branches()
    {
        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table('branches');
        $crud->set_subject('Organization Branches');
        $crud->fields('bran_name', 'online', 'inhouse');

        $crud->display_as('bran_name', 'Branch name')
            ->display_as('online', 'Online Services')
            ->display_as('inhouse', 'Inhouse Services');

        $crud->set_relation_n_n('online', 'branchesToOnline', 'online_service',
            'branch_id', 'serviceID', 'service');
        $crud->set_relation_n_n('inhouse', 'branchesToInhouse', 'inhouse_service',
            'branch_id', 'serviceID', 'service');

        $crud->columns('bran_name', 'online', 'inhouse');
        $crud->where('organization_id', $this->session->userdata('user_id'));
        $crud->unset_read();
        $crud->unset_edit();
        $crud->unset_add();
        $crud->unset_delete();
        $data['page_title'] = 'Organization Branches';
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
    }

    /*****************************************************************************/
    /**
     * change account password
     * @return mixed
     */
    /*****************************************************************************/
    public function accountSettings()
    {
        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_subject('Account Settings');

        if ($this->session->userdata('empl_id')) {
            $crud->fields('password', 'logo');
            $crud->set_table('organizationEmployees');
            $crud->set_primary_key('id');
            $crud->columns('employeName', 'email', 'logo', 'jobID');
            $crud->set_relation('jobID', 'jobs', 'job_title');
            $crud->where('organizationEmployees.id', $this->session->userdata('empl_id'));


        } else {
            $crud->fields('password', 'logo');
            $crud->set_table('organizations');
            $crud->set_primary_key('id');
            $crud->columns('organization_name', 'online', 'inhouse',
                'numberOfBranches', 'numberOfUsers', 'DayToExpire');
            $crud->display_as('numberOfBranches', 'Number of branches')
                ->display_as('numberOfUsers', 'Number of users')
                ->display_as('inactive_timeout', 'Inactive timeout per seconds')
                ->display_as('DayToExpire', 'Expiry Date');

            $crud->set_relation_n_n('online', 'organizationToOnline', 'online_service',
                'organization_id', 'serviceID', 'service');
            $crud->set_relation_n_n('inhouse', 'organizationToInhouse', 'inhouse_service',
                'organization_id', 'serviceID', 'service');
            $crud->where('organizations.id', $this->session->userdata('user_id'));


            $state = $crud->getState();

            if (($state == 'edit') OR ($state == 'update_validation')) {
                $crud->set_rules('password', 'Password', 'xss_clean|min_length[8]|max_length[25]|trim|callback_is_password_strong_edit');
                $crud->set_js('assets/grocery_crud/js/myCustom.js');
            }

            $crud->change_field_type('password', 'password');

            //Callback to hash the password before update
            $crud->callback_before_update(function ($post_array) {

                if ($post_array['password'] == '') {
                    unset($post_array['password']);
                }else{
                    $post_array['password'] = md5($post_array['password']);
                }

                return $post_array;
            });

            $crud->unset_edit_fields(array('online', 'inhouse'));

//            $crud->edit_fields('password', 'logo', 'inactive_timeout');

//            $crud->field_type('online', 'readonly');
//            $crud->field_type('inhouse', 'readonly');

        }
        $crud->set_field_upload('logo', 'assets/nav');
        $crud->unset_read();
        $crud->unset_add();
        $crud->unset_delete();
        $data['page_title'] = 'Account Settings';
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
    }



    /*****************************************************************************/
    /**
     * Suggested Branch Module
     * @return mixed
     */
    /*****************************************************************************/
    function suggestedBranchSection()
    {
        $userPermission = array('suggestedBranchSection' => null);
        if ($this->session->userdata('empl_id')) {
            $admin_user = $this->Organization_model->checkAdmin($this->session->userdata('empl_id'));
            if ($admin_user['type'] != 'admin') {
                $userPermission = $this->Organization_model->checkPermission($this->session->userdata('empl_id'), 'suggestedBranchSection');
                if ((!$userPermission['suggestedBranchSection'])) {
                    show_404();
                }
            }

        }

        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table('suggestedBranches');
        $crud->set_subject('Suggested Branch');
        $crud->fields('branch', 'organization_id');
        $crud->display_as('branch', 'Suggested Branch');
        $crud->required_fields('branch');
        $crud->columns('branch');
        $crud->change_field_type('organization_id', 'hidden');
        $crud->order_by('id', 'desc');

        $crud->set_rules('branch', 'Branch', 'xss_clean|required');
        $crud->set_rules('organization_id', 'Oranization', 'xss_clean');

        $crud->callback_before_insert(array($this, 'id_field_add'));
        $crud->callback_after_insert(array($this, 'log_user_after_insert'));
        $crud->callback_after_update(array($this, 'log_user_after_update'));
        $crud->callback_after_delete(array($this, 'user_after_delete'));

        $crud->where('organization_id', $this->session->userdata('user_id'));
        if (!empty($userPermission)) {
            if ($userPermission['suggestedBranchSection'] == 'view') {
                $crud->unset_add();
                $crud->unset_edit();
                $crud->unset_delete();
            }
        }


        $crud->unset_read();
        $data['page_title'] = "Suggested Branch";
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
    }

    /*****************************************************************************/
    /**
     * Questions Management Module
     * @return mixed
     */
    /*****************************************************************************/
    public function questionSection()
    {
        $userPermission = array('questionSection' => null);
        if ($this->session->userdata('empl_id')) {
            $admin_user = $this->Organization_model->checkAdmin($this->session->userdata('empl_id'));
            if ($admin_user['type'] != 'admin') {
                $userPermission = $this->Organization_model->checkPermission($this->session->userdata('empl_id'), 'questionSection');
                if ((!$userPermission['questionSection'])) {
                    show_404();
                }
            }

        }

        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table('questions');
        $crud->set_subject('Questions Management');
        $crud->fields('quesition', 'group_id', 'organization_id', 'hidden');
        $crud->display_as('quesition', 'Question')->display_as('group_id', 'Group Name');
        $crud->required_fields('quesition', 'group_id');

        $crud->order_by('id', 'desc');

        $crud->columns('sector', 'group_id', 'quesition', 'hidden');
        $crud->change_field_type('organization_id', 'hidden');
        $crud->display_as('quesition', 'Question');

        //validation rules
        $crud->set_rules('quesition', 'Question', 'xss_clean|required');
        $crud->set_rules('group_id', 'Group Name', 'xss_clean|required');
        $crud->set_rules('hidden', 'hidden', 'xss_clean|required');

        $crud->set_relation('group_id', 'groups', '{group_name}');

        $crud->callback_delete(array($this,'delete_question'));

        $crud->callback_column('sector', array($this, '_callback_getSector'));
        $crud->callback_before_insert(array($this, 'id_field_add'));
        $crud->callback_after_insert(array($this, 'log_user_after_insert'));
        $crud->callback_after_update(array($this, 'log_user_after_update'));
        $crud->callback_after_delete(array($this, 'user_after_delete'));

        if (!empty($userPermission)) {
            if ($userPermission['questionSection'] == 'view') {
                $crud->unset_add();
                $crud->unset_edit();
                $crud->unset_delete();
            }
        }
        $crud->unset_read();
        $crud->where('organization_id', $this->session->userdata('user_id'));
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
     * Employee Management Module
     * @return mixed
     */
    /*****************************************************************************/
    public function employee()
    {
        $userPermission = array('employeeSection' => null);
        if ($this->session->userdata('empl_id')) {
            $admin_user = $this->Organization_model->checkAdmin($this->session->userdata('empl_id'));
            if ($admin_user['type'] != 'admin') {
                $userPermission = $this->Organization_model->checkPermission($this->session->userdata('empl_id'), 'employeeSection');
                if ((!$userPermission['employeeSection'])) {
                    show_404();
                }
            }
        }

        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table('organizationEmployees');
        $crud->set_subject('Employees Management');

        $crud->set_relation_n_n('branches', 'branchesToEmployes', 'branches',
            'employee_id', 'branch_id', 'bran_name');
        $crud->set_relation('jobID', 'jobs', 'job_title');

        $crud->fields('employeName', 'email', 'password', 'logo', 'organization_id',
            'jobID', 'branches', 'type', 'questionSection', 'happinessSection', 'reportSection', 'suggestedBranchSection', 'screenSaverSection',
            'employeeSection', 'birthdateSection', 'backLogSection', 'customerBehaviourSection', 'suggestedBranchReport',
            'reviewsComments', 'webviewSection', 'employeeReport');

        $crud->change_field_type('password', 'password');
        $crud->change_field_type('organization_id', 'hidden');

        //validation rules
        $state = $crud->getState();


        if (($state == 'add') OR ($state == 'insert_validation')) {
            $crud->set_rules('password', 'Password', 'xss_clean|min_length[8]|max_length[25]|trim|required|callback_is_password_strong_add');
            $crud->required_fields('employeName', 'email', 'password', 'jobID', 'type', 'branches');
        } elseif (($state == 'edit') OR ($state == 'update_validation')) {
            $crud->set_rules('password', 'Password', 'xss_clean|min_length[8]|max_length[25]|trim|callback_is_password_strong_edit');
            $crud->set_js('assets/grocery_crud/js/myCustom.js');
            $crud->required_fields('employeName', 'email', 'jobID', 'type', 'branches');
        }

        $crud->set_rules('email', 'Email', 'xss_clean|valid_email|trim|required|callback_emp_email_check');
        $crud->set_rules('branches', 'Branchers', 'xss_clean|callback_validate_branches');
        $crud->set_rules('logo', 'Logo', 'xss_clean');
        $crud->set_rules('employeName', 'Employee Name', 'required|xss_clean|callback_emp_name_check');
        $crud->set_rules('jobID', 'Job', 'required|xss_clean');
        $crud->set_rules('type', 'Type', 'required|xss_clean');

        $crud->set_field_upload('logo', 'assets/nav');
        $crud->columns('employeName', 'email', 'jobID', 'branches');
        $crud->field_type('branches', 'multiselect');
        $crud->display_as('email', 'Employee Email')->
        display_as('employeName', 'Employee Name')->
        display_as('logo', 'Employee Image')->
        display_as('jobID', 'Job');

        $crud->order_by('id', 'desc');

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

        //callback for upload valid image
        $crud->callback_before_upload(array($this, '_callback_check_image'));

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
            $post_array['organization_id'] = $this->session->userdata('user_id');
            return $post_array;
        });
        $crud->callback_before_update(function ($post_array) {
            if ($post_array['password'] == '') {
                unset($post_array['password']);
            }else{
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
        $crud->callback_after_insert(function ($post_array, $primary) {
            $this->db->where('id', $this->session->userdata('user_id'));
            $this->db->where('numberOfUsers >', 0);
            $this->db->set('numberOfUsers', 'numberOfUsers-1', false);
            $this->db->update('organizations');
            $user_logs_insert = array(
                "organization_id" => $this->session->userdata('user_id'),
                "actionTable" => 'organizationEmployees',
                'actionOnID' => $primary
            );
            if ($this->session->userdata('empl_id')) {
                $user_logs_insert['actionPerformerID'] = $this->session->userdata('empl_id');
                $user_logs_insert['action'] = $this->session->userdata('user_full_name') . ' add new user ' . $post_array['employeName'];
            } else {
                $user_logs_insert['actionPerformerID'] = $this->session->userdata('user_id');
                $user_logs_insert['action'] = 'Organization Owner add new user ' . $post_array['employeName'];
            }
            $this->db->insert('organizationBacklog', $user_logs_insert);
            return true;
        });
        $crud->callback_after_update(function ($post_array, $primary) {
            $user_logs_insert = array(
                "organization_id" => $this->session->userdata('user_id'),
                "actionTable" => 'organizationEmployees',
                'actionOnID' => $primary
            );
            if ($this->session->userdata('empl_id')) {
                $user_logs_insert['actionPerformerID'] = $this->session->userdata('empl_id');
                $user_logs_insert['action'] = $this->session->userdata('user_full_name') . ' change info of user ' . $post_array['employeName'];
            } else {
                $user_logs_insert['actionPerformerID'] = $this->session->userdata('user_id');
                $user_logs_insert['action'] = 'Organization Owner change info of user ' . $post_array['employeName'];
            }
            $this->db->insert('organizationBacklog', $user_logs_insert);
            return true;
        });
        $crud->callback_after_delete(array($this, 'user_after_delete'));

        if (!empty($userPermission)) {
            if ($userPermission['employeeSection'] == 'view') {
                $crud->unset_add();
                $crud->unset_edit();
                $crud->unset_delete();
            }
        }
        $crud->where('organization_id', $this->session->userdata('user_id'));
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
        $id = $this->uri->segment(4);
        if (!empty($id) && is_numeric($id)) {
            $name_old = $this->db->where(array("id" => $id, 'organization_id' => $this->session->userdata('user_id')))->get('organizationEmployees')->row()->email;
            if ($str == $name_old) {
                return true;
            } else {
                $query = $this->db->get_where('organizationEmployees', array('email' => $str, 'organization_id' => $this->session->userdata('user_id')));
                if ($query->num_rows() > 0) {
                    $this->form_validation->set_message('emp_email_check', 'The Employee email already exists');
                    return false;
                } else {
                    return true;
                }
            }
        } else {
            $query = $this->db->get_where("organizationEmployees", array('email' => $str, 'organization_id' => $this->session->userdata('user_id')));
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
        $id = $this->uri->segment(4);
        if (!empty($id) && is_numeric($id)) {
            $name_old = $this->db->where(array("id" => $id, 'organization_id' => $this->session->userdata('user_id')))->get('organizationEmployees')->row()->employeName;
            if ($str == $name_old) {
                return true;
            } else {
                $query = $this->db->get_where('organizationEmployees', array('employeName' => $str, 'organization_id' => $this->session->userdata('user_id')));
                if ($query->num_rows() > 0) {
                    $this->form_validation->set_message('emp_name_check', 'The Employee Name already exists');
                    return false;
                } else {
                    return true;
                }
            }
        } else {
            $query = $this->db->get_where("organizationEmployees", array('employeName' => $str, 'organization_id' => $this->session->userdata('user_id')));
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
            $message = 'Branches field is required';
            $this->form_validation->set_message('validate_branches', $message);

            return false;
        } else {
            foreach ($branches as $branch) {
                $this->db->select('*');
                $this->db->from('branches');
                $this->db->where('organization_id', $this->session->userdata('user_id'));
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
            $this->db->where('id', $this->session->userdata('user_id'));
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
        $orgID = $this->session->userdata('user_id');
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

    // callback function for validating strong password
    public function is_password_strong_add($password)
    {
        if (preg_match('#[0-9]#', $password) && preg_match('#[a-zA-Z]#', $password)) {
            return true;
        }
        $this->form_validation->set_message('is_password_strong', 'Week Password');
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
     * Backlog Module Add Edit delete.
     *
     * @return mixed
     */
    /*****************************************************************************/
    function backLogSection()
    {
        $userPermission = array('backLogSection' => null);
        if ($this->session->userdata('empl_id')) {
            $admin_user = $this->Organization_model->checkAdmin($this->session->userdata('empl_id'));
            if ($admin_user['type'] != 'admin') {
                $userPermission = $this->Organization_model->checkPermission($this->session->userdata('empl_id'), 'backLogSection');
                if ((!$userPermission['backLogSection'])) {
                    show_404();
                }
            }

        }
        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table('organizationBacklog');
        $crud->set_subject('Backlog');
        $crud->fields('action', 'inserted_time');
        $crud->display_as('inserted_time', 'Action Time');
        $crud->columns('action', 'inserted_time');
        $crud->callback_column('action', array($this, '_full_text'));
        $crud->where('organization_id', $this->session->userdata('user_id'));
        $crud->order_by('id', 'desc');
        $crud->unset_add();
        $crud->unset_edit();
        $crud->unset_delete();
        $crud->unset_read();
        $data['page_title'] = "Backlog";
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
    }

    function _full_text($value, $row)
    {
        return $value = wordwrap($row->action, 255, "<br>", true);
    }

    /*****************************************************************************/
    /**
     * Birthdate Module Add Edit delete.
     *
     * @return mixed
     */
    /*****************************************************************************/
    function birthdateSection()
    {
        $userPermission = array('birthdateSection' => null);
        $query = array();
        if ($this->session->userdata('empl_id')) {
            $admin_user = $this->Organization_model->checkAdmin($this->session->userdata('empl_id'));

            if ($admin_user['type'] != 'admin') {
                $userPermission = $this->Organization_model->checkPermission($this->session->userdata('empl_id'), 'birthdateSection');
                if ((!$userPermission['birthdateSection'])) {
                    show_404();
                }

            }
            $this->db->select('branch_id');
            $this->db->from('branchesToEmployes');
            $this->db->where('employee_id', $this->session->userdata('empl_id'));
            $query = $this->db->get()->result_array();
            $branches = array_column($query, 'branch_id');

        } else {
            $this->db->select('id');
            $this->db->from('branches');
            $this->db->where('organization_id', $this->session->userdata('user_id'));
            $query = $this->db->get()->result_array();
            $query = array_column($query, 'branch_id');
        }


        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table('guests_accounts');
        $crud->set_subject('Guests Birthdate');
        $crud->fields('name', 'phone', 'gender', 'email', 'birthdate');
        $crud->columns('name', 'phone', 'gender', 'email', 'birthdate');
        $crud->where('guests_accounts.organization_id', $this->session->userdata('user_id'));

        $crud->unset_add();
        $crud->unset_edit();
        $crud->unset_delete();
        $crud->unset_read();
        $data['page_title'] = "Guests Birthdate";
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
    }

    /*****************************************************************************/
    /**
     * Birthdate Module Add Edit delete.
     *
     * @return mixed
     */
    /*****************************************************************************/
    function happinessSection()
    {
        $userPermission = array('happinessSection' => null);
        $query = array();
        if ($this->session->userdata('empl_id')) {
            $admin_user = $this->Organization_model->checkAdmin($this->session->userdata('empl_id'));
            if ($admin_user['type'] != 'admin') {
                $userPermission = $this->Organization_model->checkPermission($this->session->userdata('empl_id'), 'happinessSection');
                if ((!$userPermission['happinessSection'])) {
                    show_404();
                }
            } else {
                $this->db->select('branch_id');
                $this->db->from('branchesToEmployes');
                $this->db->where('employee_id', $this->session->userdata('empl_id'));
                $query = $this->db->get()->result_array();
                $query = array_column($query, 'branch_id');
            }
        } else {
            $this->db->select('id');
            $this->db->from('branches');
            $this->db->where('organization_id', $this->session->userdata('user_id'));
            $query = $this->db->get()->result_array();
            $query = array_column($query, 'id');
        }
        if ((!$userPermission['happinessSection']) && $this->session->userdata('empl_id')) {
            show_404();
        }

        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table('guests_accounts');
        $crud->set_subject('Happiness Review');
        $crud->fields('id', 'name', 'phone', 'gender', 'email', 'birthdate');
        $crud->columns('name', 'phone', 'gender', 'email', 'birthdate');
        $crud->where('guests_accounts.organization_id', $this->session->userdata('user_id'));

        $crud->callback_column('name', array($this, '_nameLink'));

        $crud->unset_add();
        $crud->unset_edit();
        $crud->unset_delete();
        $crud->unset_read();
        $data['page_title'] = "Happiness Review";
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
    }

    function _nameLink($value, $row)
    {
        return $value = '<a href="getReviews?grd=' . $row->id . '"><b><u>' . $row->name . '</u></b></a>';
    }

    /*****************************************************************************/
    /**
     * Guest Review Module Add Edit delete.
     *
     * @return mixed
     */
    /*****************************************************************************/
    function getReviews()
    {
        if (!isset($_GET['grd']) || !is_numeric($_GET['grd'])) {
            show_404();
        }
        $userPermission = array('happinessSection' => null);
        $query = array();
        if ($this->session->userdata('empl_id')) {
            $userPermission = $this->Organization_model->checkPermission($this->session->userdata('empl_id'), 'happinessSection');
            $this->db->select('branch_id');
            $this->db->from('branchesToEmployes');
            $this->db->where('employee_id', $this->session->userdata('empl_id'));
            $query = $this->db->get()->result_array();
            $query = array_column($query, 'branch_id');
        } else {
            $this->db->select('id');
            $this->db->from('branches');
            $this->db->where('organization_id', $this->session->userdata('user_id'));
            $query = $this->db->get()->result_array();
            $query = array_column($query, 'id');
        }
        if ((!$userPermission['happinessSection']) && $this->session->userdata('empl_id')) {
            show_404();
        }

        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table('client_data');
        $crud->set_subject('Client Data');
        $crud->set_primary_key('review_id');

        $crud->columns('bran_name', 'review_time', 'group_name', 'quesition', 'result');

        $crud->where('organization_id', $this->session->userdata('user_id'));
        $crud->where('guest_id', $_GET['grd']);
        $crud->display_as('bran_name', 'Branch Name')->display_as('quesition', 'Question');
        $crud->callback_column('result', array($this, '_setResult'));


        $crud->unset_add();
        $crud->unset_edit();
        $crud->unset_delete();
        $crud->unset_read();
        $data['page_title'] = "Client Details";
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
    }

    function _setResult($value, $row)
    {
        if ($value == '3') {
            return '100%';
        } else if ($value == '2') {
            return '50%';

        } else if ($value == '1') {
            return '0%';

        }
    }

    /*****************************************************************************/
    /**
     * Guest Review Module Add Edit delete.
     *
     * @return mixed
     */
    /*****************************************************************************/
    function reportSection()
    {
        $userPermission = array('reportSection' => null);

        $query = array();
        $branches = array();
        if ($this->session->userdata('empl_id')) {
            $admin_user = $this->Organization_model->checkAdmin($this->session->userdata('empl_id'));

            if ($admin_user['type'] != 'admin') {
                $userPermission = $this->Organization_model->checkPermission($this->session->userdata('empl_id'), 'reportSection');
                if ((!$userPermission['reportSection'])) {
                    show_404();
                }

            }
            $this->db->select('id,bran_name');
            $this->db->from('branchesToEmployes');
            $this->db->where('employee_id', $this->session->userdata('empl_id'));
            $this->db->join('branches','branchesToEmployes.branch_id = branches.id');
            $query = $this->db->get()->result_array();
            $data['branches'] = $query;
            $branches = array_column($query, 'id');

        } else {
            $this->db->select('id,bran_name');
            $this->db->from('branches');
            $this->db->where('organization_id', $this->session->userdata('user_id'));
            $query = $this->db->get()->result_array();
            $data['branches'] = $query;
            $branches = array_column($query, 'id');
        }

        $branchesIN = implode(',', $branches);

        $query = $this->db->get('groupsSectors');
        $result = $query->result_array();
        $data['sectors'] = $result;

        $data['genders'] = ['male', 'female'];

        $query = $this->db->get('groups');
        $result = $query->result_array();
        $data['groups'] = $result;

        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table('reports');
        $crud->set_subject('Report');
        $crud->set_primary_key('review_id');

        $crud->order_by('review_id', 'desc');

        $crud->columns('name', 'group_name', 'percent', 'phone', 'email', 'Age', 'gender', 'bran_name', 'sector', 'review_time');



        $crud->where('reports.organization_id', $this->session->userdata('user_id'));

        if (isset($_POST['genders']) && $_POST['genders'] != '') {
            $gender = $_POST['genders'];
            $crud->where('gender', $gender);
        }

        if (isset($_POST['max']) && isset($_POST['min']) && $_POST['max'] != '' && $_POST['min'] != '') {
            if (is_int((int)$_POST['max']) && is_int((int)$_POST['min'])) {
                $crud->where("Age BETWEEN {$_POST['min']} AND {$_POST['max']}");
            }
        }

        if (isset($_POST['groups']) && $_POST['groups'] != '') {
            $group = $_POST['groups'];
            $crud->where('group_id', $group);
        }

        if (isset($_POST['daterange']) && $_POST['daterange'] != '') {
            $dates = explode('-', $_POST['daterange']);
            $dates[0] = date('Y-m-d', strtotime($dates[0])) . " 00:00:00";
            $dates[1] = date('Y-m-d', strtotime($dates[1])) . " 23:59:59";
            $crud->where("review_time BETWEEN '{$dates[0]}' AND '{$dates[1]}'");
        }

        if (isset($_POST['branches'])) {
            $branchesIN = implode("', '", $_POST['branches']);
            $crud->where("bran_name IN ('" . $branchesIN . "')");
        }

        if (isset($_POST['sectors'])) {
            $sectorsIN = implode("', '", $_POST['sectors']);
            $crud->where("sector IN ('" . $sectorsIN . "')");
        }



        $crud->unset_add();
        $crud->unset_edit();
        $crud->unset_delete();
        $crud->unset_read();
        $data['page_title'] = "General Report";
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
    }
//*****************************************************************************
//callbacks for logging after insert and after update and before delete
    function selectTable()
    {
        $table = array('screenSaverSection' => 'screen_server',
            'questionSection' => 'questions',
            'employee' => 'organizationEmployees',
            'suggestedBranchSection' => 'suggestedBranches'
        );
        return $table[$this->uri->segment(2)];
    }

    function selectAction($post_array)
    {
        $table = array(
            'questionSection' => ' question ' . $post_array['quesition'],
            'employee' => ' user ' . $post_array['employeName'],
            'suggestedBranchSection' => '  suggested branch ' . $post_array['branch']
        );
//        $table = array('screenSaverSection' => ' screen saver ' . $post_array['image'],
//            'questionSection' => ' question ' . $post_array['quesition'],
//            'employee' => ' user ' . $post_array['employeName'],
//            'suggestedBranchSection' => '  suggested branch ' . $post_array['branch']
//        );
        return $table[$this->uri->segment(2)];
    }

    function log_user_after_insert($post_array, $primary_key)
    {
        $user_logs_insert = array(
            "organization_id" => $this->session->userdata('user_id'),
            "actionTable" => $this->selectTable(),
            'actionOnID' => $primary_key
        );
        if ($this->session->userdata('empl_id')) {
            $user_logs_insert['actionPerformerID'] = $this->session->userdata('empl_id');
            $user_logs_insert['action'] = $this->session->userdata('user_full_name') . ' add new' . $this->selectAction($post_array);
        } else {
            $user_logs_insert['actionPerformerID'] = $this->session->userdata('user_id');
            $user_logs_insert['action'] = 'Organization Owner add new' . $this->selectAction($post_array);
        }
        $this->db->insert('organizationBacklog', $user_logs_insert);
        return true;
    }

    function log_user_after_update($post_array, $primary_key)
    {
        $user_logs_insert = array(
            "organization_id" => $this->session->userdata('user_id'),
            "actionTable" => $this->selectTable(),
            'actionOnID' => $primary_key
        );
        if ($this->session->userdata('empl_id')) {
            $user_logs_insert['actionPerformerID'] = $this->session->userdata('empl_id');
            $user_logs_insert['action'] = $this->session->userdata('user_full_name') . ' change' . $this->selectAction($post_array);
        } else {
            $user_logs_insert['actionPerformerID'] = $this->session->userdata('user_id');
            $user_logs_insert['action'] = 'Organization Owner change' . $this->selectAction($post_array);
        }
        $this->db->insert('organizationBacklog', $user_logs_insert);

        return true;
    }

    public function user_after_delete($primary_key)
    {
        $table = array('screenSaverSection' => ' screen saver ',
            'questionSection' => ' question ',
            'employee' => ' user ',
            'suggestedBranchSection' => '  suggested branch '
        );
        $user_logs_insert = array(
            "organization_id" => $this->session->userdata('user_id'),
            "actionTable" => $this->selectTable(),
            'actionOnID' => $primary_key
        );
        if ($this->session->userdata('empl_id')) {
            $user_logs_insert['actionPerformerID'] = $this->session->userdata('empl_id');
            $user_logs_insert['action'] = $this->session->userdata('user_full_name') .
                ' Delete a ' . $table[$this->uri->segment(2)];
        } else {
            $user_logs_insert['actionPerformerID'] = $this->session->userdata('user_id');
            $user_logs_insert['action'] = 'Organization Owner' .
                ' Delete a ' . $table[$this->uri->segment(2)];
        }
        return $this->db->insert('organizationBacklog', $user_logs_insert);
    }

    /*****************************************************************************/
    /**
     * Backlog Module Add Edit delete.
     *
     * @return mixed
     */
    /*****************************************************************************/
    function webview()
    {
        $query = array();
        $branches = array();

        if ($this->session->userdata('empl_id')) {
            $admin_user = $this->Organization_model->checkAdmin($this->session->userdata('empl_id'));

            if ($admin_user['type'] != 'admin') {
                $userPermission = $this->Organization_model->checkPermission($this->session->userdata('empl_id'), 'webviewSection');
                if ((!$userPermission['webviewSection'])) {
                    show_404();
                }

            }
            $this->db->select('branch_id');
            $this->db->from('branchesToEmployes');
            $this->db->where('employee_id', $this->session->userdata('empl_id'));
            $query = $this->db->get()->result_array();
            $branches = array_column($query, 'branch_id');

        } else {
            $this->db->select('id');
            $this->db->from('branches');
            $this->db->where('organization_id', $this->session->userdata('user_id'));
            $query = $this->db->get()->result_array();
            $branches = array_column($query, 'id');
        }
        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table('webviewLinks');
        $crud->set_primary_key('branch_id');
        $crud->set_subject('Branches Links');
        $crud->fields('bran_name', 'link', 'branch_id');
        $crud->display_as('bran_name', 'Branch Name')->display_as('link', 'Branch Link');
        $crud->columns('bran_name', 'link');
        $crud->callback_column('link', array($this, '_linkGenerate'));

        for ($i = 0; $i < sizeof($branches); $i++) {
            $crud->or_where('branch_id', $branches[$i]);
        }

        $crud->order_by('bran_name', 'asc');

        $crud->unset_add();
        $crud->unset_edit();
        $crud->unset_delete();
        $crud->unset_read();
        $data['page_title'] = "Branches Links";
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
    }

    function _linkGenerate($value, $row)
    {
        if (preg_match('/Fast Food/', $value)) {
            return '<a target="_blank" href="' . base_url() . $value . '">' . 'Fast Food' . '</a> ';
        }
        if (preg_match('/Delivery/', $value)) {
            return '<a target="_blank" href="' . base_url() . $value . '">' . 'Delivery' . '</a> ';
        }
        if (preg_match('/Catering/', $value) && !preg_match('/isOwner/', $value)) {
            return '<a target="_blank" href="' . base_url() . $value . '">' . 'Catering Guests' . '</a> ';
        }
        if (preg_match('/isOwner/', $value)) {
            return '<a target="_blank" href="' . base_url() . $value . '">' . 'Catering Owner' . '</a> ';
        }

    }

    /*****************************************************************************/
    /**
     * First step for customer Behaviour.
     *
     * @return mixed
     */
    /*****************************************************************************/
    function customerBehaviour($guestID = null, $reviewID = null, $groupID = null)
    {
        $query = array();
        $branches = array();

        if ($this->session->userdata('empl_id')) {
            $admin_user = $this->Organization_model->checkAdmin($this->session->userdata('empl_id'));

            if ($admin_user['type'] != 'admin') {
                $userPermission = $this->Organization_model->checkPermission($this->session->userdata('empl_id'), 'customerBehaviourSection');
                if ((!$userPermission['customerBehaviourSection'])) {
                    show_404();
                }
            }
            $this->db->select('id,bran_name');
            $this->db->from('branchesToEmployes');
            $this->db->where('employee_id', $this->session->userdata('empl_id'));
            $this->db->join('branches','branchesToEmployes.branch_id = branches.id');
            $query = $this->db->get()->result_array();
            $data['branches'] = $query;
            $branches = array_column($query, 'id');


        } else {

            $this->db->select('id,bran_name');
            $this->db->from('branches');
            $this->db->where('organization_id', $this->session->userdata('user_id'));
            $query = $this->db->get()->result_array();
            $data['branches'] = $query;
            $branches = array_column($query, 'id');
        }


        $branchesIN = implode(',', $branches);

        $query = $this->db->get('groupsSectors');
        $result = $query->result_array();
        $data['sectors'] = $result;

        $data['genders'] = ['male', 'female'];

        $unique_id = ($this->session->userdata('empl_id')) ? $this->session->userdata('empl_id') : $this->session->userdata('user_id');
        if (!$guestID & !$reviewID & !$groupID) {
            if (isset($_POST['branches'])) {
                $branchesIN = implode(',', $_POST['branches']);
            }

            $this->db->query("DROP TABLE IF EXISTS customerbehavoiur$unique_id");
            $queryString = "CREATE  TABLE customerbehavoiur$unique_id As SELECT X.id AS id, X.rev_id AS rev_id, X.NAME AS name, X.phone AS phone, X.email AS email,
			 X.gender AS gender, X.review_time AS review_time, X.Age AS age,B.bran_name AS branch,X.sector AS sector, 
			 ROUND( ( ( SUM( ( CASE WHEN(X.result = 2) THEN(X.resultx * 0.5)
			 WHEN(X.result = 1) THEN(X.resultx * 0) ELSE X.resultx END ) ) / SUM(X.resultx) ) * 100 ), 2 ) AS latest_review, 
			 ROUND( ( ( SUM( ( CASE WHEN(Y.result = 2) THEN(Y.resultx * 0.5)
			 WHEN(Y.result = 1) THEN(Y.resultx * 0) ELSE Y.resultx END ) ) / SUM(Y.resultx) ) * 100 ), 2 ) AS all_reviews
			 FROM all_reviews_data Y, last_review_data X 
			 inner join branches AS B on (B.id = X.branch_id)  
			 WHERE X.id = Y.id and X.branch_id IN ($branchesIN)";

            if (isset($_POST['max']) && isset($_POST['min']) && $_POST['max'] != '' && $_POST['min'] != '') {

                if (is_int((int)$_POST['max']) && is_int((int)$_POST['min'])) {

                    $queryString .= " and X.Age BETWEEN {$_POST['min']} AND {$_POST['max']}";
                }
            }

            if (isset($_POST['daterange']) && $_POST['daterange'] != '') {
                $dates = explode('-', $_POST['daterange']);
                $dates[0] = date('Y-m-d', strtotime($dates[0])) . " 00:00:00";
                $dates[1] = date('Y-m-d', strtotime($dates[1])) . " 23:59:59";
                $queryString .= " and X.review_time BETWEEN '{$dates[0]}' AND '{$dates[1]}' ";
            }

            if (isset($_POST['sectors'])) {

                $sectorsIN = implode("', '", $_POST['sectors']);
                $queryString .= " and X.sector IN  ('" . $sectorsIN . "') ";

            }

            if (isset($_POST['genders']) && $_POST['genders'] != '') {
                $gender = $_POST['genders'];
                $queryString .= " and X.gender = '$gender'";
            }

            $queryString .= " GROUP BY
			 X.rev_id, X.id, X.NAME, X.phone, X.email, X.gender, X.review_time, X.branch_id , X.sector";

//            echo $queryString;
            $this->db->query($queryString);

            $crud = new grocery_CRUD();
            $crud->set_theme('datatables');
            $crud->set_table("customerbehavoiur$unique_id");
            $crud->set_primary_key('id');
            $crud->set_subject('Customer Behavior');
            $crud->fields('id', 'name', 'phone', 'email', 'gender', 'age', 'branch', 'sector', 'latest_review', 'all_reviews');
            $crud->columns('name', 'phone', 'email', 'gender', 'age', 'branch', 'sector', 'latest_review', 'all_reviews');
            $crud->callback_column('latest_review', array($this, '_addpercent'));
            $crud->callback_column('all_reviews', array($this, '_addpercent'));
            $crud->callback_column('name', array($this, '_detailslink'));
        } else if (is_numeric($guestID) & !$reviewID & !$groupID) {
            $unique_id = ($this->session->userdata('empl_id')) ? $this->session->userdata('empl_id') : $this->session->userdata('user_id');

            $this->db->select('*');
            $this->db->from("customerbehavoiur$unique_id");
            $this->db->where('id', $guestID);
            $data['client'] = $this->db->get()->result_array();
            $this->db->select('branch_id as id,bran_name,review_time,percent');
            $this->db->from('percentPerReview');
            $this->db->join('branches', 'branches.id = percentPerReview.branch_id', 'left');
            $this->db->where('percentPerReview.id', $guestID);
            if (isset($_GET['datarange'])) {
                $dates = explode('-', $_GET['datarange']);
                $dates[0] = date('Y-m-d', strtotime($dates[0])) . " 00:00:00";
                $dates[1] = date('Y-m-d', strtotime($dates[1])) . " 23:59:59";
                $this->db->where('review_time >=', $dates[0]);
                $this->db->where('review_time <=', $dates[1]);
            }
            $this->db->order_by("review_time", "asc");
            $query = $this->db->get()->result_array();

            $data['empNames'] = array_values(array_unique(array_column($query, 'bran_name')));
            for ($i = 0; $i < sizeof($query); $i++) {
                for ($j = 0; $j < sizeof($data['empNames']); $j++) {
                    if ($data['empNames'][$j] == $query[$i]['bran_name']) {
                        $data['questions'][$j][] = $query[$i]['review_time'];
                    }
                }
            }

            for ($i = 0; $i < sizeof($data['empNames']); $i++) {
                for ($j = 0; $j < sizeof($data['empNames']); $j++) {
                    $data['comparingchartPercent'][] = array_fill(0, sizeof($data['questions'][$j]), 0);
                }
            }
            foreach ($query as $row) {
                $rowindex = array_search($row['bran_name'], $data['empNames']);
                $colindex = array_search($row['review_time'], $data['questions'][$rowindex]);
                $data['comparingchartPercent'][$rowindex][$colindex] = $row['percent'];
            }

            $crud = new grocery_CRUD();
            $crud->set_theme('datatables');
            $crud->set_table('percentPerReview');
            $crud->set_primary_key('id');
            $crud->set_subject('Customer Behaviuor');
            $crud->fields('id', 'rev_id', 'review_time', 'percent', 'branch_id');
            $crud->columns('review_time', 'percent', 'branch_id');

            $crud->set_relation('branch_id', 'branches', 'bran_name');
            $crud->display_as('branch_id', 'Branch Name');
            $crud->where('percentPerReview.id', $guestID);
            if (isset($_GET['datarange'])) {
                $dates = explode('-', $_GET['datarange']);
                $dates[0] = date('Y-m-d', strtotime($dates[0])) . " 00:00:00";
                $dates[1] = date('Y-m-d', strtotime($dates[1])) . " 23:59:59";
                $crud->where('review_time >=', $dates[0]);
                $crud->where('review_time <=', $dates[1]);
            }

            $branches[0] = 'branch_id =' . $branches[0];
            $branchesIN = implode(' OR branch_id =', $branches);

            $crud->where("($branchesIN)", null, FALSE);
            $crud->callback_column('percent', array($this, '_addpercent'));
            $crud->callback_column('review_time', array($this, '_reviewlink'));

        } else if (is_numeric($guestID) & is_numeric($reviewID) & !$groupID) {
            $unique_id = ($this->session->userdata('empl_id')) ? $this->session->userdata('empl_id') : $this->session->userdata('user_id');
            $this->db->select('*');
            $this->db->from("customerbehavoiur$unique_id");
            $this->db->where('id', $guestID);
            $data['client'] = $this->db->get()->result_array();
            $this->db->select('bran_name,percent');
            $this->db->from('percentPerReview');
            $this->db->join('branches', 'branches.id = percentPerReview.branch_id', 'left');
            $this->db->where('rev_id', $reviewID);
            $data['review'] = $this->db->get()->result_array();

            $crud = new grocery_CRUD();
            $crud->set_theme('datatables');
            $crud->set_table('percentPerGroup');
            $crud->set_primary_key('id');
            $crud->set_subject('Customer Behaviour');
            $crud->fields('id', 'rev_id', 'group_id', 'percent', 'group_comment');
            $crud->columns('group_id', 'percent', 'group_comment');

            $crud->set_relation('group_id', 'groups', 'group_alias');
            $crud->display_as('group_id', 'Group Name');
            $crud->where('percentPerGroup.rev_id', $reviewID);
            $branches[0] = 'branch_id =' . $branches[0];
            $branchesIN = implode(' OR branch_id =', $branches);

            $crud->where("($branchesIN)", null, FALSE);
            $crud->callback_column('percent', array($this, '_addpercent'));
            $crud->callback_column('s0e939a4f', array($this, '_grouplink'));
        } else if (is_numeric($guestID) & is_numeric($reviewID) & is_numeric($groupID)) {
            $unique_id = ($this->session->userdata('empl_id')) ? $this->session->userdata('empl_id') : $this->session->userdata('user_id');
            $this->db->select('*');
            $this->db->from("customerbehavoiur$unique_id");
            $this->db->where('id', $guestID);
            $data['client'] = $this->db->get()->result_array();
            $this->db->select('bran_name,percent');
            $this->db->from('percentPerReview');
            $this->db->join('branches', 'branches.id = percentPerReview.branch_id', 'left');
            $this->db->where('rev_id', $reviewID);
            $data['review'] = $this->db->get()->result_array();

            $crud = new grocery_CRUD();
            $crud->set_theme('datatables');
            $crud->set_table('questionPerPercent');
            $crud->set_primary_key('id');
            $crud->set_subject('Customer Behaviuor');
            $crud->fields('id', 'group_id', 'question_id', 'result');
            $crud->columns('group_id', 'question_id', 'result');
            $crud->set_relation('question_id', 'questions', 'quesition');
            $crud->set_relation('group_id', 'groups', 'group_alias');
            $crud->display_as('group_id', 'Group Name')->display_as('question_id', 'Question');
            $crud->where('questionPerPercent.group_id', $groupID);
            $crud->where('questionPerPercent.review_id', $reviewID);

            $branches[0] = 'branch_id =' . $branches[0];
            $branchesIN = implode(' OR branch_id =', $branches);

            $crud->where("($branchesIN)", null, FALSE);
            $crud->callback_column('result', array($this, '_resultTopercent'));
        } else {
            show_404();
        }
        $crud->unset_add();
        $crud->unset_edit();
        $crud->unset_delete();
        $crud->unset_read();
        $data['page_title'] = "Customer Behavior";
        $data['crud_output'] = $crud->render();
        $this->admin_view('event_crud_output', $data);
    }

    function _addpercent($value, $row)
    {
        return $value . '%';
    }

    function _resultTopercent($value, $row)
    {
        if ($value == 3) {
            return '100%';
        } else if ($value == 2) {
            return '50%';
        } else {
            return '0%';
        }
    }

    function _detailslink($value, $row)
    {
        $url = '<a target="_blank" href="' . base_url() . 'organization/customerBehaviour/' . $row->id;
        if (isset($_POST['daterange'])) {
            $url .= "?datarange={$_POST['daterange']}";
        }
        $url .= '"><b>' . $value . '</b></a> ';
        return $url;
    }

    function _reviewlink($value, $row)
    {
        return '<a target="_blank" href="' . base_url() . 'organization/customerBehaviour/' . $this->uri->segment(3) . '/' . $row->rev_id . '"><b>' . $value . '</b></a> ';
    }

    function _grouplink($value, $row)
    {
        return '<a target="_blank" href="' . base_url() . 'organization/customerBehaviour/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $row->group_id . '"><b>' . $value . '</b></a> ';
    }


    function suggestedBranches()
    {
        $query = array();
        $branches = array();

        if ($this->session->userdata('empl_id')) {
            $admin_user = $this->Organization_model->checkAdmin($this->session->userdata('empl_id'));

            if ($admin_user['type'] != 'admin') {
                $userPermission = $this->Organization_model->checkPermission($this->session->userdata('empl_id'), 'suggestedBranchReport');
                if ((!$userPermission['suggestedBranchReport'])) {
                    show_404();
                }

            }
            $this->db->select('id,bran_name');
            $this->db->from('branchesToEmployes');
            $this->db->where('employee_id', $this->session->userdata('empl_id'));
            $this->db->join('branches','branchesToEmployes.branch_id = branches.id');
            $query = $this->db->get()->result_array();
            $data['branches'] = $query;
            $branches = array_column($query, 'id');

        } else {
            $this->db->select('id,bran_name');
            $this->db->from('branches');
            $this->db->where('organization_id', $this->session->userdata('user_id'));
            $query = $this->db->get()->result_array();
            $data['branches'] = $query;
            $branches = array_column($query, 'id');

        }


        $query = $this->db->get('groupsSectors');

        $result = $query->result_array();
        $data['sectors'] = $result;

        $data['genders'] = ['male', 'female'];

        $branchesIN = implode(',', $branches);

        $queryString = "";
        $unique_id = ($this->session->userdata('empl_id')) ? $this->session->userdata('empl_id') : $this->session->userdata('user_id');

//        $crud->where("branch_id IN (" . $branchesIN . ")");


        $this->db->query("DROP TABLE IF EXISTS suggestedBranchesReport$unique_id");

        if (isset($_POST['branches'])) {
            $branchesIN = implode(',', $_POST['branches']);
        }


        $queryString = "CREATE TABLE suggestedBranchesReport$unique_id AS 
        SELECT  R.id,G.name AS name,G.phone AS phone,G.gender AS gender,B.bran_name AS branch_name,SB.branch AS suggested_branch_name,R.sector AS sector,R.review_time AS review_time
        FROM reviews R
        LEFT JOIN guests_accounts G on (R.guest_id = G.id)
        LEFT JOIN branches B on (R.branch_id = B.id)
        LEFT JOIN suggestedBranches SB on (R.sugg_branch_id = SB.id)
        WHERE R.branch_id IN ($branchesIN)";

        if (isset($_POST['daterange']) && $_POST['daterange'] != '') {
            $dates = explode('-', $_POST['daterange']);
            $dates[0] = date('Y-m-d', strtotime($dates[0])) . " 00:00:00";
            $dates[1] = date('Y-m-d', strtotime($dates[1])) . " 23:59:59";
            $queryString .= " and R.review_time BETWEEN '{$dates[0]}' AND '{$dates[1]}' ";
        }

        if (isset($_POST['sectors'])) {
            $sectorsIN = implode("', '", $_POST['sectors']);
            $queryString .= " and R.sector IN  ('" . $sectorsIN . "') ";
        }


        if (isset($_POST['genders']) && $_POST['genders'] != '') {
            $gender = $_POST['genders'];
            $queryString .= " and G.gender = '$gender'";
        }

        $this->db->query($queryString);

        $org_id = $this->session->userdata('user_id');

        $query = $this->db->query("select SB.suggested_branch_name AS 'Branch Name', count(SB.suggested_branch_name) AS 'Count'
            from suggestedBranchesReport$unique_id SB
            left join branches B on (SB.branch_name = B.bran_name)
            where B.organization_id = $org_id
            group by SB.suggested_branch_name
            ");

        $result = $query->result_array();

        $data['summary'] = $result;

        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table("suggestedBranchesReport$unique_id");
        $crud->set_primary_key('id');
        $crud->set_subject('Suggested Branches');
        $crud->fields('id', 'name', 'phone', 'gender', 'branch_name', 'suggested_branch_name', 'sector', 'review_time');
        $crud->columns('name', 'phone', 'gender', 'branch_name', 'suggested_branch_name', 'sector', 'review_time');

        $crud->order_by('id', 'desc');
        $crud->display_as('name', 'Guest Name');
        $crud->display_as('phone', 'Guest Phone');
        $crud->display_as('gender', 'Gender');
        $crud->display_as('branch_name', 'Branch');
        $crud->display_as('suggested_branch_name', 'Suggested Branch');

        $crud->unset_add();
        $crud->unset_edit();
        $crud->unset_delete();
        $crud->unset_read();
        $data['page_title'] = "Suggested Branches";
        $data['crud_output'] = $crud->render();

        $this->admin_view('event_crud_output', $data);


    }


    function reviewsComments()
    {
        $query = array();
        $branches = array();

        if ($this->session->userdata('empl_id')) {
            $admin_user = $this->Organization_model->checkAdmin($this->session->userdata('empl_id'));

            if ($admin_user['type'] != 'admin') {
                $userPermission = $this->Organization_model->checkPermission($this->session->userdata('empl_id'), 'reviewsComments');
                if ((!$userPermission['reviewsComments'])) {
                    show_404();
                }

            }
            $this->db->select('id,bran_name');
            $this->db->from('branchesToEmployes');
            $this->db->where('employee_id', $this->session->userdata('empl_id'));
            $this->db->join('branches','branchesToEmployes.branch_id = branches.id');
            $query = $this->db->get()->result_array();
            $data['branches'] = $query;
            $branches = array_column($query, 'id');

        } else {
            $this->db->select('id,bran_name');
            $this->db->from('branches');
            $this->db->where('organization_id', $this->session->userdata('user_id'));
            $query = $this->db->get()->result_array();
            $data['branches'] = $query;
            $branches = array_column($query, 'id');
        }

        $query = $this->db->get('groupsSectors');
        $result = $query->result_array();
        $data['sectors'] = $result;

        $query = $this->db->get('groups');
        $result = $query->result_array();
        $data['groups'] = $result;

        $data['genders'] = ['male', 'female'];

        $branchesIN = implode(',', $branches);

        $queryString = "";
        $unique_id = ($this->session->userdata('empl_id')) ? $this->session->userdata('empl_id') : $this->session->userdata('user_id');

//        $crud->where("branch_id IN (" . $branchesIN . ")");


        $this->db->query("DROP TABLE IF EXISTS reviewscomments$unique_id");

        if (isset($_POST['branches'])) {
            $branchesIN = implode(',', $_POST['branches']);
        }


        $queryString = "CREATE TABLE reviewscomments$unique_id AS 
       SELECT R.id,GA.name AS 'name',GA.phone AS 'phone',GA.gender AS 'gender',B.bran_name AS 'branch',R.sector AS 'sector',G.group_name AS 'group',
        RD.group_comment AS 'comment',R.review_time AS 'review_time' FROM review_data RD
        left join groups G on (RD.group_id = G.id)
        left join reviews R  on (RD.review_id = R.id)
        left join guests_accounts GA on (R.guest_id = GA.id)
        left join branches B on (R.branch_id = B.id)
        WHERE R.branch_id IN ($branchesIN) AND RD.group_comment != ''";

        if (isset($_POST['daterange']) && $_POST['daterange'] != '') {
            $dates = explode('-', $_POST['daterange']);
            $dates[0] = date('Y-m-d', strtotime($dates[0])) . " 00:00:00";
            $dates[1] = date('Y-m-d', strtotime($dates[1])) . " 23:59:59";
            $queryString .= " and R.review_time BETWEEN '{$dates[0]}' AND '{$dates[1]}' ";
        }

        if (isset($_POST['sectors'])) {
            $sectorsIN = implode("', '", $_POST['sectors']);
            $queryString .= " and R.sector IN  ('" . $sectorsIN . "') ";
        }

        if (isset($_POST['genders']) && $_POST['genders'] != '') {
            $gender = $_POST['genders'];
            $queryString .= " and GA.gender = '$gender'";
        }

        if (isset($_POST['groups']) && $_POST['groups'] != '') {
            $groups = implode("', '", $_POST['groups']);
            $queryString .= " and G.id = '$groups'";
        }

        $queryString .= " GROUP BY R.id,GA.name,GA.phone,GA.gender,R.sector,B.bran_name,G.group_name,RD.group_comment,R.review_time";

        $this->db->query($queryString);

//        echo $this->db->last_query();die();

        $crud = new grocery_CRUD();
        $crud->set_theme('datatables');
        $crud->set_table("reviewscomments$unique_id");
        $crud->set_primary_key('id');
        $crud->set_subject('Comments');
        $crud->fields('id', 'name', 'phone', 'gender', 'branch', 'sector', 'group', 'comment', 'review_time');
        $crud->columns('name', 'phone', 'gender', 'branch', 'sector', 'group', 'comment', 'review_time');
        $crud->order_by('id', 'desc');

        $crud->display_as('name', 'Guest Name');
        $crud->display_as('phone', 'Guest Phone');
        $crud->display_as('gender', 'Gender');


        $crud->unset_add();
        $crud->unset_edit();
        $crud->unset_delete();
//        $crud->unset_read();
        $data['page_title'] = "Comments Reports";
        $data['crud_output'] = $crud->render();

        $this->admin_view('event_crud_output', $data);


    }


}
