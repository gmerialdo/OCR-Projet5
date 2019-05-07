<?php

class Event
{

    protected $_url;
    public $_idEvent;
    public $_name;
    public $_description;

    public function __construct($uri){

    }








$req = [
            "fields"  => [
                'event_id',
                'location',
                'name',
                'description',
                'image',
                'DATE_FORMAT(start_datetime, \'%W\') AS "start_weekday"',
                'DATE_FORMAT(start_datetime, \'%b %D, %Y\') AS "start_date"',
                'DATE_FORMAT(start_datetime, \'%r\') AS "start_time"',
                'DATE_FORMAT(finish_datetime, \'%W\') AS "finish_weekday"',
                'DATE_FORMAT(finish_datetime, \'%b %D, %Y\') AS "finish_date"',
                'DATE_FORMAT(finish_datetime, \'%r\') AS "finish_time"',
                'type_tickets',
                'public',
                'members_only',
                'LEAST (price_adult_member, price_adult, price_child_member, price_child) AS "smallest_price"'
            ],
            "from"  => "evt_events",
            "where" => [ "active_event = 1" ]
        ];
        $data = Model::select($req);



//-------------------------OLD PROJECT 4---------------------------------------
    //get all comments for a post
    public function getComments($idPost){
        $req = [
            "fields"  => [
                'id AS "{{ comment_id }}"',
                'author AS "{{ author }}"',
                'comment AS "{{ comment }}"',
                'DATE_FORMAT(date, \'%d/%m/%Y\') AS "{{ comment_date }}"',
                'idPost AS "{{ idPost }}"'
            ],
            "from"  => "comments",
            "where" => [
                "idPost = ".$idPost,
                "valid != 0"
            ]
        ];
        $data = Model::select($req);
        return $data["data"];
    }

    //get all comments with 'valid'=$valid
    public function getCommentsV($valid){
        $req = [
            "fields"  => [
                'id AS "{{ comment_id }}"',
                'author AS "{{ author }}"',
                'comment AS "{{ comment }}"',
                'DATE_FORMAT(date, \'%d/%m/%Y\') AS "{{ comment_date }}"',
                'idPost AS "{{ idPost }}"'
            ],
            "from"  => "comments",
            "where" => [
                "valid = ".$valid
            ]
        ];
        $data = Model::select($req);
        return $data["data"];
    }

    //count number of comments for a post
    public function countComments($idPost){
        $req = [
            "fields" => [
                'COUNT( *) AS "nb_comments"'
            ],
            "from" => "comments",
            "where" => [
                "idPost = ".$idPost,
                "valid != 0"
            ]
        ];
        $data = Model::select($req);
        return $data["data"];
    }

    //count number of valid comments
    public function countCommentsV($valid){
        $req = [
            "fields" => [
                'COUNT( *) AS "nb"'
            ],
            "from" => "comments",
            "where" => [
                "valid = ".$valid
            ]
        ];
        $data = Model::select($req);
        return $data["data"];
    }

    //add a comment
    public function addComment($data){
        $req = [
            "table"  => "comments",
            "fields" => [
                'author',
                'comment',
                'date',
                'idPost',
                'valid'
            ]
        ];
        return Model::insert($req, $data);
    }

    //signal a comment : valid becomes 2
    public function signalComment($id){
        //returns true if comment has been correctly signaled
        $req = [
            "table"  => "comments",
            "fields" => [
                'valid'
            ],
            "where" => [
                "id = ".$id
            ]
        ];
        //updates "valid" into 2 for this comment to add in admin list of comments to check
        return Model::update($req, [2]);
    }

    //accept a comment : valid becomes 1 (moderation by admin)
    public function acceptComment($id){
        $req = [
            "table"  => "comments",
            "fields" => [
                'valid'
            ],
            "where" => [
                "id = ".$id
            ]
        ];
        return Model::update($req, [1]);
    }

    //refuse a comment : valid becomes 0 (moderation by admin)
    public function refuseComment($id){
        $req = [
            "table"  => "comments",
            "fields" => [
                'valid'
            ],
            "where" => [
                "id = ".$id
            ]
        ];
        return Model::update($req, [0]);
    }

}
