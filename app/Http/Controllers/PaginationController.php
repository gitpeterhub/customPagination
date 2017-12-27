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


            //$last_rem_item = $offset%$totalItems>0;

        $sql = "SELECT COUNT(*) AS rowCount FROM users";

        $count = DB::select($sql);

        $packet["count"] = $count[0]->rowCount;
        $totalItems = $packet["count"];

        //Calculating pagination and pagination view here
        $prev=NULL;
        $next=NULL;

        $items_per_page = $limit;

        $totalPages= ceil($totalItems / $limit);

        //$currentPage=floor($start / $limit);

        $offset = ($page - 1) * $items_per_page;

        $last_offset = ($totalPages - 1) * $items_per_page;

        $previous_page = ($page - 1);

        $next_page = ($page + 1);


        if ($page==1) {

        	$prev = NULL;
        	$next = '<a class="badge" onclick="requestData('.($next_page).','.$limit.')">NEXT</a>';
        	
        
        }elseif ($page==$totalPages) {
        	$next = NULL;
        	$prev = '<a class="badge" onclick="requestData('.($previous_page).','.$limit.')">PREV</a>';
        	
        	
        }else{

        	$prev = '<a class="badge" onclick="requestData('.($previous_page).','.$limit.')">PREV</a>';
        	$next = '<a class="badge" onclick="requestData('.($next_page).','.$limit.')">NEXT</a>';
        }

        	/*$prev = '<a class="badge" onclick="requestData('.($previous_page).','.$limit.')">PREV</a>';
        	$next = '<a class="badge" onclick="requestData('.($next_page).','.$limit.')">NEXT</a>';*/

        /*if ($previous_offset > 0) echo '<a href="?start='.$previous_offset.'&limit='.$items_per_page.'>prev</a>';


		if ($next_offset <= $totalPages * $items_per_page) echo '<a href="?start='.$next_offset.'&limit='.$items_per_page.'">prev</a>';*/


        $packet["pagination_links"] = $prev.$next;

        // retrieving data as per requested offset and limit from database table

         $sql = "SELECT * FROM users LIMIT $offset, $limit";

         //Executing the sql statement and assigning in an indexed array
        $packet["data"] = DB::select($sql);


        return $packet;
    }
}
