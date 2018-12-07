<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mobile extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->session->sess_expiration = '180';
    }

    public function logout()
    {
        $this->session->sess_destroy();
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
        redirect('mobile/login');
    }

    /*****************************************************************************/
    /**
     * login Function.
     *
     * @return mixed
     */
    /*****************************************************************************/
    public function login()
    {

        if (isset($_GET['b']) && isset($_GET['s'])) {
            if (preg_match("/^[A-Z0-9]*$/", $_GET['b']) &&
                in_array($_GET['s'], array('Catering', 'Delivery', 'Fast Food'), true)) {
                if ($this->session->userdata('user_exist')) {
                    redirect('mobile/survey');
                }
                if ($this->session->userdata('user_Notexist')) {
                    redirect('mobile/signup');
                }
                if (isset($_GET['isOwner'])) {
                    $this->session->set_userdata(array('isOwner' => 1));
                }
                $this->db->select('organization_id,id');
                $this->db->from('branches');
                $this->db->where('uniqueID', $_GET['b']);
                $query = $this->db->get()->result_array();
                if ($query) {
                    $this->db->select('logo');
                    $this->db->from('organizations');
                    $this->db->where('id', $query[0]['organization_id']);
                    $qOrg = $this->db->get()->row_array();

                    $this->session->set_userdata(array('logo' => $qOrg['logo'], 'branchKey' => $_GET['b'],
                        'sector' => $_GET['s'], 'organization_id' => $query[0]['organization_id']
                    , 'branch_id' => $query[0]['id']));
                    $this->load->view('mob/login.php', $qOrg);
                } else {
                    show_404();
                }

            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }



    /*****************************************************************************/
    /**
     * signup Function.
     *
     * @return mixed
     */
    /*****************************************************************************/
    public function signup()
    {
        if (!$this->session->userdata('user_exist') && !$this->session->userdata('user_Notexist')) {
            redirect('mobile/login');
        }
        if ($this->session->userdata('user_exist')) {
            redirect('mobile/survey');
        }
        $this->load->view('mob/signup.php');
    }
    /*****************************************************************************/
    /**
     * signup Function.
     *
     * @return mixed
     */
    /*****************************************************************************/
    public function survey()
    {
        if (!$this->session->userdata('user_exist') && !$this->session->userdata('user_Notexist')) {
            redirect('mobile/login');
        }
        if ($this->session->userdata('user_Notexist')) {
            redirect('mobile/signup');
        }

        $sector = $this->session->userdata('sector');
        $restaurant_id = $this->session->userdata('organization_id');
        $branch_id = $this->session->userdata('branch_id');


        $data = array();

        $post = array('branch_id' => $branch_id, 'restaurant_id' => $restaurant_id, 'sector' => $sector);


        $data['employees'] = $this->sendrequest('loademp', $post);

        if (array_key_exists('result', $data['employees'])) {

            $this->load->view('mob/noemployee.php');
        } else {
            $post = array('restaurant_id' => $this->session->userdata('organization_id'));
            $url = 'loadquestion/';
            if ($this->session->userdata('sector') == 'Catering') {
                $url .= 2;
                if ($this->session->userdata('isOwner')) {
                    $url .= '/1';
                }
            }
            if ($this->session->userdata('sector') == 'Delivery') {
                $url .= 1;
            }
            if ($this->session->userdata('sector') == 'Fast Food') {
                $url .= 3;
            }

            $data['questions'] = $this->sendrequest($url, $post);

            $post = array('restaurant_id' => $this->session->userdata('organization_id'));
            $data['nextBranches'] = $this->sendrequest('nextbran', $post);
            $this->load->view('mob/survey.php', $data);
        }

    }
    /*****************************************************************************/
    /**
     * login execution Function.
     *
     * @return mixed
     */
    /*****************************************************************************/
    public function doLogin()
    {
        if ($this->session->userdata('user_exist')) {
            redirect('mobile/survey');
        }

        if (isset($_POST['phone']) && preg_match("^((0)(1)(0|1|2)([0-9]{8}))$^", $_POST['phone'])) {
            $data = array('user_phone' => $_POST['phone'],
                'restaurant_id' => $this->session->userdata('organization_id'));
            $result = $this->sendrequest('login', $data);
            $this->session->set_userdata(array('user_phone' => $_POST['phone']));
            if ($result['result'] == "true") {
                $this->session->set_userdata(array('user_exist' => true));
                $this->session->set_userdata(array('user_id' => $result['user_id']));
                redirect('mobile/survey');
            } else {
                $this->session->set_userdata(array('user_Notexist' => true));
                redirect('mobile/signup');
            }
        } else {
            show_404();
        }
    }

    /*****************************************************************************/
    /**
     * signup execution Function.
     *
     * @return mixed
     */
    /*****************************************************************************/
    public function doSignup()
    {
        if (!$this->session->userdata('user_exist') && !$this->session->userdata('user_Notexist')) {
            redirect('mobile/login');
        }
        if ($this->session->userdata('user_exist')) {
            redirect('mobile/survey');
        }
        if (!isset($_POST['email']) && !isset($_POST['gender']) && !isset($_POST['name']) && !isset($_POST['birthdate'])) {
            show_404();
        }

        $validEmail = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        $validGender = in_array($_POST['gender'], array('male', 'female'), true);
        $validName = preg_match("/[A-Za-z]+/", $_POST['name']);
        $validBirthdate = preg_match("^(([0-9]{4})(\-)([0-9]{2})(\-)([0-9]{2}))$^", $_POST['birthdate']);

        if ($validEmail && $validGender && $validName && $validBirthdate) {
            $data = array('user_phone' => $this->session->userdata('user_phone'),
                'restaurant_id' => $this->session->userdata('organization_id'),
                'user_name' => $_POST['name'],
                'gender' => $_POST['gender'],
                'birthdate' => $_POST['birthdate'],
                'email' => $_POST['email']);
            $result = $this->sendrequest('signup', $data);
            $this->session->set_userdata(array('user_exist' => true));
            $this->session->set_userdata(array('user_Notexist' => false));
            $this->session->set_userdata(array('user_id' => $result['user_id']));
            redirect('mobile/survey');
        } else {
            show_404();
        }
    }
    /*****************************************************************************/
    /**
     * signup execution Function.
     *
     * @return mixed
     */
    /*****************************************************************************/
    public function saveReview()
    {
        if (!$this->session->userdata('user_exist') && !$this->session->userdata('user_Notexist')) {
            redirect('mobile/login');
        }

        if (!isset($_POST['reviewData'])) {
            show_404();
        }
        $data = array();
        $data['questions'] = $_POST['reviewData'];
        $data['branch_id'] = $this->session->userdata('branch_id');
        $data['phone'] = $this->session->userdata('user_phone');
        $data['restaurant_id'] = $this->session->userdata('organization_id');
        $data['sector'] = $this->session->userdata('sector');
        if (isset($_POST['nextB'])) {
            if ($_POST['nextB']) {
                $data['next_branch_id'] = $_POST['nextB'];
            }
        }
        $result = $this->sendrequest('savereview', $data);


        $this->session->sess_destroy();

        $this->load->view('mob/sent.php');


    }

    /*****************************************************************************/
    /**
     * sending request and get response Function.
     *
     * @return mixed
     */
    /*****************************************************************************/
    private function sendrequest($url, $data)
    {
        $url = base_url() . "service/$url";

        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($options);
        $result = json_decode(file_get_contents($url, false, $context), true);
        return $result;
    }
}
