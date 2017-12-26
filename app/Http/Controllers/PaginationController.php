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
    	$offset = $req->get("offset");
    	$filter = $req->get("filter");

    	//return $limit;

    	$datas = $this->findAll($offset,$limit);

    	echo json_encode($datas);

    }

    public function findAll($offset, $limit, $filter=NULL)
    {


       /* $extra_sql = "";

        if ($filter) {


            $category_id = $filter['category_id'];



            $extra_sql = " where g.image_category_id=".$category_id;

            }
*/



         $sql = "SELECT * FROM users LIMIT $offset, $limit";


        $packet["data"] = DB::select($sql);

        $sql = "SELECT COUNT(*) AS rowCount FROM users";

        $count = DB::select($sql);

        $packet["count"] = $count[0]->rowCount;

        return $packet;
    }
}
