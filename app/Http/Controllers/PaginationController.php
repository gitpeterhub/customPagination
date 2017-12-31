<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class PaginationController extends Controller
{
    //

    public function index(){

    	return view("pagination");
    }

    public function list(Request $req){

    	//get limit, offset and other extra filter payloads for creating paginated datas 
    	$limit = $req->get("limit");
    	$page = $req->get("page");
    	$filter = $req->get("filter");

    	//return $limit;

    	$datas = $this->paginateData($page,$limit,$filter);

    	echo json_encode($datas);

    }

    public function paginateData($page, $limit, $filter=NULL)
    {


        $extra_sql = "";

        if ($filter) {


            $extra_sql = "WHERE name LIKE '%$filter%'";

            }

            //Getting total counts form the concerned database table


        $sql = "SELECT COUNT(*) AS rowCount FROM users $extra_sql";

        $count = DB::select($sql);

        $packet["count"] = $count[0]->rowCount;
        $totalItems = $packet["count"];

        if ($totalItems == "") {
            return "Sorry, no data available for now!";
        }

    
        $paginatedData = $this->simplePagination($page,$limit,$totalItems);

       //$paginatedData = $this->numberedPagination($page,$limit,$totalItems);

		$packet["pagination_links"] = $paginatedData["pagination_links"];

        $offset = $paginatedData['offset'];

        // retrieving data as per requested offset and limit from database table

         $sql = "SELECT * FROM users $extra_sql LIMIT $offset, $limit";

         //Executing the sql statement and assigning in an indexed array
        $packet["data"] = DB::select($sql);


        return $packet;
    }


    public function simplePagination($page,$limit,$totalItems){

        $pagination_parameters = $this->calculatePagination($page,$limit,$totalItems);

        if ($pagination_parameters["totalPages"] < 2 &&  $totalItems < $limit) {

            $prev=NULL;
            $next=NULL;
            
        }else{

            if ($page==1) {

                $prev = NULL;
                $next = '<a class="badge" onclick="requestData('.$pagination_parameters["next_page"].','.$limit.')">NEXT</a>';
                
            
            }elseif ($page==$pagination_parameters["totalPages"]) {
                $next = NULL;
                $prev = '<a class="badge" onclick="requestData('.$pagination_parameters["previous_page"].','.$limit.')">PREV</a>';
                
                
            }else{

                $prev = '<a class="badge" onclick="requestData('.$pagination_parameters["previous_page"].','.$limit.')">PREV</a>';
                $next = '<a class="badge" onclick="requestData('.$pagination_parameters["next_page"].','.$limit.')">NEXT</a>';
            }

     }


        return [
                "offset" => $pagination_parameters["offset"],
                "pagination_links" => $prev.$next
            ];

    }




    public function numberedPagination($page,$limit,$totalItems){

        $pagination_parameters = $this->calculatePagination($page,$limit,$totalItems);

        $prev=NULL;
        $next=NULL;
        $first = NULL;
        $last = NULL;
        $numbered_links = "";
        $max_pagination_number_at_a_time = 5; /// limit for pagination number view at a time for large no of data..
      

        if ($pagination_parameters["totalPages"] > $max_pagination_number_at_a_time) {  //////this is for large number of data...
        
            $first = '<a class="badge" onclick="requestData(1,'.$limit.')">First</a>';
            $last = '<a class="badge" onclick="requestData('.$pagination_parameters["last_page"].','.$limit.')">Last</a>';
            //---------this is the logic area to always make sure the pagination link show equal to or smaller than max_pagination_number_at_a_time----///////////

            $i = 1;
            $i_limiter = $max_pagination_number_at_a_time+1;
            $update_i_by = floor($page/$max_pagination_number_at_a_time);


                if (($page - $update_i_by*$max_pagination_number_at_a_time) == 1) {

                    $i = ($update_i_by*$max_pagination_number_at_a_time)+1;
                    $i_limiter = $i+$max_pagination_number_at_a_time;

                }else {

                            if (($page - $update_i_by*$max_pagination_number_at_a_time) == 0) {
                               
                               $i = ($update_i_by*$max_pagination_number_at_a_time)+1-$max_pagination_number_at_a_time;

                                $i_limiter = $i+$max_pagination_number_at_a_time;
                            }else{

                                $i = ($update_i_by*$max_pagination_number_at_a_time)+1;

                                $i_limiter = $i+$max_pagination_number_at_a_time;
                            }                        
                        
                    }
               

                while ( $i < $i_limiter) {

                    if ($i==$page) {

                        $numbered_links .= '<a class="badge link-active" onclick="requestData('.($i).','.$limit.')">'.$i.'</a>';
                    }else{

                        $numbered_links .= '<a class="badge" onclick="requestData('.($i).','.$limit.')">'.$i.'</a>';
                    }

                    if ($i==$pagination_parameters["totalPages"]) {
                        break;
                    }

                    $i++;
                }  

                //---------End of the logic area to always make sure the pagination link show equal to or smaller than max_pagination_number_at_a_time----/////////// 


        }else{  /// this is for small number of data...

            ///---- if smaller than max_pagination_number_at_a_time  we donot wanna show first and last link in pagination--///  
             $first = NULL;
             $last = NULL;

              ////-------------End of donot show first and last link for smaller than max_pagination_number_at_a_time--///

            for($i = 1;$i <= $pagination_parameters["totalPages"];$i++) 
            {     
                if ($i==$page) {

                    $numbered_links .= '<a class="badge link-active" onclick="requestData('.($i).','.$limit.')">'.$i.'</a>';
                }else{

                    $numbered_links .= '<a class="badge" onclick="requestData('.($i).','.$limit.')">'.$i.'</a>';
                }
                
            }

        }


        ///---calculate pagination view and links for varities of conditions-----/////////////////

        if ($pagination_parameters["totalPages"] < 2 &&  $totalItems < $limit) {

            /// ----we don't wanna show any pagination link for above conditions-----/////
            $prev=NULL;
            $next=NULL;
            $numbered_links = NULL;

        }else{

            if ($page==1) {

            $prev = NULL;
            $next = $last.'<a class="badge" onclick="requestData('.$pagination_parameters["next_page"].','.$limit.')">>></a>';
            
        
            }elseif ($page==$pagination_parameters["totalPages"]) {
                $next = NULL;
                $prev = '<a class="badge" onclick="requestData('.$pagination_parameters["previous_page"].','.$limit.')"><<</a>'.$first;
                
                
            }else{

                $prev = '<a class="badge" onclick="requestData('.$pagination_parameters["previous_page"].','.$limit.')" ><<</a>'.$first;
                $next = $last.'<a class="badge" onclick="requestData('.$pagination_parameters["next_page"].','.$limit.')" >>></a>';
            }
        }


         ///---End of calculate pagination view and links for varities of conditions-----/////////////////


        $pagination_links = $prev.$numbered_links.$next;

        return [
                "offset" => $pagination_parameters["offset"],
                "pagination_links" => $pagination_links
            ];
        
    }



    public function calculatePagination($page,$limit,$totalItems){


        //---Calculating pagination determining parameters---------------     

        $items_per_page = $limit;

        $totalPages= ceil($totalItems / $limit);

        $offset = ($page - 1) * $items_per_page;

        $previous_page = ($page - 1);

        $next_page = ($page + 1);

        $last_page = $totalPages;

        //----End of pagination calculation------------------

        return [
                "offset" => $offset,
                "totalPages" => $totalPages,
                 "next_page" => $next_page,
                 "previous_page" => $previous_page,
                 "last_page" => $last_page
             ];
        
    }


}
