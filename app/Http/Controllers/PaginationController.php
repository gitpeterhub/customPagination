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

    	$datas = $this->findAll($page,$limit);

    	echo json_encode($datas);

    }

    public function findAll($page, $limit, $filter=NULL)
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

        //---Calculating pagination determining parameters---------------     

        $items_per_page = $limit;

        $totalPages= ceil($totalItems / $limit);

        //$currentPage=floor($start / $limit);

        $offset = ($page - 1) * $items_per_page;

        //$last_offset = ($totalPages - 1) * $items_per_page;

        $previous_page = ($page - 1);

        $next_page = ($page + 1);

        //----End of pagination calculation------------------

        ////---------start of two types of pagination views--------------------/////////////

        $prev=NULL;
        $next=NULL;

        ///////Type-A////This is a simple prev and next pagination/////////

        /*if ($page==1) {

        	$prev = NULL;
        	$next = '<a class="badge" onclick="requestData('.($next_page).','.$limit.')">NEXT</a>';
        	
        
        }elseif ($page==$totalPages) {
        	$next = NULL;
        	$prev = '<a class="badge" onclick="requestData('.($previous_page).','.$limit.')">PREV</a>';
        	
        	
        }else{

        	$prev = '<a class="badge" onclick="requestData('.($previous_page).','.$limit.')">PREV</a>';
        	$next = '<a class="badge" onclick="requestData('.($next_page).','.$limit.')">NEXT</a>';
        }


        $packet["pagination_links"] = $prev.$next;*/

        /////Type-A///////Simple pagination ends here/////////////////////////

        ////Type-B///////Numbered pagination starts here////////////////////////


        if ($page==1) {

            $prev = NULL;
            $next = '<a class="badge" onclick="requestData('.($next_page).','.$limit.')">>></a>';
            
        
        }elseif ($page==$totalPages) {
            $next = NULL;
            $prev = '<a class="badge" onclick="requestData('.($previous_page).','.$limit.')"><<</a>';
            
            
        }else{

            $prev = '<a class="badge" onclick="requestData('.($previous_page).','.$limit.')" ><<</a>';
            $next = '<a class="badge" onclick="requestData('.($next_page).','.$limit.')" >>></a>';
        }

        $numbered_links = "";
        $max_pagination_number_at_a_time = 5; /// limit for pagination number view for large no of data..

        if ($totalPages > $max_pagination_number_at_a_time) {  //////this is for large number of data...
            
            $i = 1;
            $i_limiter = 0;
            $update_i_by = floor($page/$max_pagination_number_at_a_time);

            if ($update_i_by > 0) {

                $i = ($update_i_by*$max_pagination_number_at_a_time)+1;
                $i_limiter = $i+5;

            }                   

                while ( $i < $i_limiter) {
                   
                   if ($page%$max_pagination_number_at_a_time==0) {
                        break;
                    }

                    if ($i==$totalPages) {
                        break;
                    }

                    if ($i==$page) {

                        $numbered_links .= '<a class="badge active" onclick="requestData('.($i).','.$limit.')">'.$i.'</a>';
                    }else{

                        $numbered_links .= '<a class="badge" onclick="requestData('.($i).','.$limit.')">'.$i.'</a>';
                    }

                    $i++;
                }  

               // return "hello is this working?";      

        }else{  /// this is for small number of data...

            for($i = 1;$i <= $totalPages;$i++) 
            {     
                if ($i==$page) {

                    $numbered_links .= '<a class="badge active" onclick="requestData('.($i).','.$limit.')">'.$i.'</a>';
                }else{

                    $numbered_links .= '<a class="badge" onclick="requestData('.($i).','.$limit.')">'.$i.'</a>';
                }
                
            }

        }

		$packet["pagination_links"] = $prev.$numbered_links.$next;

        /////Type-B//////Numbered pagination ends here////////////////////////


        ///----------End of two types of pagination views----------------///////////////////////////

        // retrieving data as per requested offset and limit from database table

         $sql = "SELECT * FROM users LIMIT $offset, $limit";

         //Executing the sql statement and assigning in an indexed array
        $packet["data"] = DB::select($sql);


        return $packet;
    }
}
