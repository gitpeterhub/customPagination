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

    	$datas = $this->paginateData($page,$limit);

    	echo json_encode($datas);

    }

    public function paginateData($page, $limit, $filter=NULL)
    {


       /* $extra_sql = "";

        if ($filter) {


            $category_id = $filter['category_id'];



            $extra_sql = " where g.image_category_id=".$category_id;

            }
*/
            //Getting total counts form the concerned database table


        $sql = "SELECT COUNT(*) AS rowCount FROM users";

        $count = DB::select($sql);

        $packet["count"] = $count[0]->rowCount;
        $totalItems = $packet["count"];

    
        //$paginatedData = $this->simplePagination($page,$limit,$totalItems);

        $paginatedData = $this->numberedPagination($page,$limit,$totalItems);

		$packet["pagination_links"] = $paginatedData["pagination_links"];

        $offset = $paginatedData['offset'];

        // retrieving data as per requested offset and limit from database table

         $sql = "SELECT * FROM users LIMIT $offset, $limit";

         //Executing the sql statement and assigning in an indexed array
        $packet["data"] = DB::select($sql);


        return $packet;
    }


    public function simplePagination($page,$limit,$totalItems){

        $pagination_parameters = $this->calculatePagination($page,$limit,$totalItems);

        $prev=NULL;
        $next=NULL;

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


        return [
                "offset" => $pagination_parameters["offset"],
                "pagination_links" => $prev.$next
            ];

    }

    public function numberedPagination($page,$limit,$totalItems){

        $pagination_parameters = $this->calculatePagination($page,$limit,$totalItems);

        $prev=NULL;
        $next=NULL;

        $first = '<a class="badge" onclick="requestData(1,'.$limit.')">First</a>';
        $last = '<a class="badge" onclick="requestData('.$pagination_parameters["last_page"].','.$limit.')">Last</a>';

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

        $numbered_links = "";
        $max_pagination_number_at_a_time = 5; /// limit for pagination number view for large no of data..

        if ($pagination_parameters["totalPages"] > $max_pagination_number_at_a_time) {  //////this is for large number of data...
             //return ceil(1/$max_pagination_number_at_a_time);

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

               // return "hello is this working?";      

        }else{  /// this is for small number of data...

            for($i = 1;$i <= $totalPages;$i++) 
            {     
                if ($i==$page) {

                    $numbered_links .= '<a class="badge link-active" onclick="requestData('.($i).','.$limit.')">'.$i.'</a>';
                }else{

                    $numbered_links .= '<a class="badge" onclick="requestData('.($i).','.$limit.')">'.$i.'</a>';
                }
                
            }
        }

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
