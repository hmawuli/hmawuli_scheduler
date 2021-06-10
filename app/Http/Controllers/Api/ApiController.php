<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use DateTime;
use GuzzleHttp\Client as GuzzleClient;

use App\ClientsModel;
use App\OrganisationModel;
use App\AppointmentsModel;
use App\TimeSlotsModel;

class ApiController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }
    
    public function addOrganisation(Request $request) {
        $validator = Validator::make($request->json()->all(), [
            "name" => "required",
            "email" => "required|email|unique:organizations",
            "password" => "required",
        ]);

        $request["password"] = bcrypt($request->password);

        if ($validator->fails()) {
            return response()->json([
                "status" => 400,
                "errors" => $validator->errors()
            ], 400);
        }

        try {
            $org = new OrganisationModel();
            $org->insert($request->all());

            return response()->json([
                "status" => 201,
                "message" => "organisation successfully created",
            ]);
        } catch(\Exception $e) {
            return response()->json([
                "status" => 500,
                "errors" => $e->getMessage()
            ], 500);
        }
    }

    public function loginOrganisation(Request $request) {
        $validator = Validator::make($request->json()->all(), [
            "email" => "required|email",
            "password" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 400,
                "errors" => $validator->errors()
            ], 400);
        }

        try {
            $org = new OrganisationModel();
            // get org details
            $details = OrganisationModel::where('email', $request->email)->get();
            
            if(count($details) == 0) {
                return response()->json([
                    "status" => 404,
                    "errors" => "organisation not found",
                ], 404);
            }
            if (Hash::check($request->password, $details[0]["password"])) {
                return response()->json([
                    "status" => 200,
                    "message" => "login successful",
                    "id" => $details[0]["id"],
                    "name" => $details[0]["name"]
                ]);
            }
            return response()->json([
                "status" => 400,
                "errors" => "password incorrect"
            ],400);
            
        } catch(\Exception $e) {
            return response()->json([
                "status" => 500,
                "errors" => $e->getMessage()
            ], 500);
        }
    }

    public function addTimeSlot(Request $request) {
        $validator = Validator::make($request->json()->all(), [
            "org_id" => "required|exists:organizations,id",
            "start_time" => "required",
            "end_time" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 400,
                "errors" => $validator->errors()
            ], 400);
        }

        if($this->isTimeSlotAvailable($request->org_id, $request->start_time, $request->end_time)){
            return response()->json([
                "status" => 400,
                "errors" => "time slot already taken"
            ], 400);
        }

        try {
            TimeSlotsModel::insert($request->all());

            return response()->json([
                "status" => 200,
                "errors" => "time slot successfully inserted"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "errors" => $e->getMessage()
            ], 500);
        }
        
    }

    private function isTimeSlotAvailable($org_id, $start_time, $end_time) {
        $sql   = " org_id = $org_id AND start_time <= '$end_time' AND end_time >= '$start_time' ";
        $slots = TimeSlotsModel::whereRaw($sql)->get();

        if (count($slots) > 0) {
            return true;
        } else {
            return false;
        }
    }

    private function isAppointmentAvailable($org_id, $date, $start_time, $end_time) {
        $sql   = " org_id = $org_id AND date = '$date' AND start_time <= '$end_time' AND end_time >= '$start_time' ";
        $slots = AppointmentsModel::whereRaw($sql)->get();

        if (count($slots) > 0) {
            return true;
        } else {
            return false;
        }
    }


    // Clients 
    public function addClient(Request $request) {
        $validator = Validator::make($request->json()->all(), [
            "last_name" => "required",
            "first_name" => "required",
            "other_names" => "nullable",
            "email" => "required|email|unique:clients",
            "phone" => "required|regex:/[0-9]{10}/",
            "password" => "required",
        ]);

        $request["password"] = bcrypt($request->password);

        if ($validator->fails()) {
            return response()->json([
                "status" => 400,
                "errors" => $validator->errors()
            ], 400);
        }

        try {
            $client = new ClientsModel();
            $client->insert($request->all());

            return response()->json([
                "status" => 201,
                "message" => "client successfully created",
            ]);
        } catch(\Exception $e) {
            return response()->json([
                "status" => 500,
                "errors" => $e->getMessage()
            ], 500);
        }
    }

    public function loginClient(Request $request) {
        $validator = Validator::make($request->json()->all(), [
            "email" => "required|email",
            "password" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 400,
                "errors" => $validator->errors()
            ], 400);
        }
        
        try {
            $org = new ClientsModel();
            // get org details
            $details = ClientsModel::where('email', $request->email)->get();
            
            if(count($details) == 0) {
                return response()->json([
                    "status" => 404,
                    "errors" => "Client not found",
                ], 404);
            }
            if (Hash::check($request->password, $details[0]["password"])) {
                return response()->json([
                    "status" => 200,
                    "message" => "login successful",
                    "id" => $details[0]["id"],
                    "name" => $details[0]["last_name"] . " " . $details[0]["first_name"] 
                ]);
            }
            return response()->json([
                "status" => 400,
                "errors" => "password incorrect"
            ],400);
            
        } catch(\Exception $e) {
            return response()->json([
                "status" => 500,
                "errors" => $e->getMessage()
            ], 500);
        }
    }

    public function bookAppointment(Request $request) {
        $validator = Validator::make($request->json()->all(), [
            "org_id" => "required|exists:organizations,id",
            "client_id" => "required|exists:clients,id",
            "date" => "required|date",
            "start_time" => "required",
            "end_time" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 400,
                "errors" => $validator->errors()
            ], 400);
        }

        if(!$this->isTimeSlotAvailable($request->org_id, $request->start_time, $request->end_time)){
            return response()->json([
                "status" => 400,
                "errors" => "time slot not available for this organisation"
            ], 400);
        }

        if($this->isAppointmentAvailable($request->org_id, $request->date, $request->start_time, $request->end_time)){
            return response()->json([
                "status" => 400,
                "errors" => "time slot already taken"
            ], 400);
        }

        try {
            AppointmentsModel::insert($request->all());

            $client = ClientsModel::where('id', $request->client_id)->get();
            $org = OrganisationModel::where('id', $request->org_id)->get();

            // send email
            $data = [
                "name" => $client[0]["first_name"] . " " . $client[0]["last_name"],
                "org_name" => $org[0]["name"],
                "email" => $client[0]["email"],
                "start_time" => $request->start_time,
                "end_time" => $request->end_time,
                "date" => $request->date,
            ];
            $this->sendEmail($data);
            $this->sendSMS($data);
            return response()->json([
                "status" => 200,
                "errors" => "appointment successfully booked"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "errors" => $e->getMessage()
            ], 500);
        }
    }

    private function sendEmail($data) {
        Mail::send("mail", $data, function($message) use ($data){
            $message->to($data["email"], $data["name"]);
            $message->from("hormekumawuli93@gmail.com", "Mawuli");
            $message->subject("Booking Appointment");
        });
    }

    private function sendSMS($data) {
        $message = "Hello " . $data['name'] . ", your appointment with ". $data['org_name'] . " is due in 15 minutes";
        $scheduleTime = date("d-m-Y h:i A", strtotime("-15 minutes", strtotime($data['date'] . " " . $data['start_time'])));

        $postData = [
            "action" => "send-sms",
            "api_key" => "OkNUOXEzNEl1QlBXUXptTU8=",
            "to" => $data["phone"],
            "from" => "Mawuli",
            "sms" => $message,
            "schedule" => $scheduleTime
        ];
       
        $client = new GuzzleClient();
        $request = $client->request('GET', 'https://sms.arkesel.com/sms/api', ['headers' => ['Accept' => 'application/json'], "json" => $postData]);
        $response = $request->getBody()->getContents();

        print_r($response);
    }


    public function test() {
        return response()->json([
            'status' => true,
            'response' => 'API Test Successful',
        ]);
    }
}
