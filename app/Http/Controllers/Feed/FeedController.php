<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Comments;
use App\Models\Feed;
use App\Models\Like;
use Illuminate\Http\Request;

class FeedController extends Controller
{   
    public function index(){
      $feeds=Feed::with('user')->latest()->get();
      return response([
        'feeds'=>$feeds,
      ],200);
    }
    public function store(PostRequest $request){
       $request->validated();
       auth()->user()->feeds()->create([
        'content'=> $request->content
       ]);
       return response([
        'message'=>'sucsess',
       ],201);
    }
    public function likePost($feed_id){
      //select feed with  feed_id
      $feed=Feed::whereId($feed_id)->first();
      if(!$feed){
         return response([
            'message'=>'404 not found',
          ], 500);
      }
      //UNliked
      
      $unlike_post =Like::where('user_id',auth()->id())->where('feed_id',$feed_id)->delete();
      if($unlike_post){
        return response([
            'message'=>'Unliked',
          ], 200);
      }
      //Like
      $like_post =Like::create([
           'user_id'=>auth()->id(),
           'feed_id'=>$feed_id
      ]);
      if($like_post){
        return response([
            'message'=>'liked',
          ], 200);
      }
      
      
    }
    public function comment(Request $request,$feed_id){
      $request->validate([
        'body'=>'required'
      ]);
      $comment=Comments::create([
        'user_id'=>auth()->id(),
        'feed_id'=>$feed_id,
        'body'=>$request->body,
      ]);
      return response([
        'message'=>'sucsess'
      ],201);
    }
    public function getComments($feed_id){
      $comments= Comments::with('feed')->with('user')->whereFeedId($feed_id)->latest()->get();
      return response([
        'comments'=>$comments
      ],200);
    }
}