<?php
namespace ProjectPress\Includes\Project\Collaboration;
if(!defined('ABSPATH')){exit;}
final class ContentRepository{
 public function for_project(string $type,int $project_id):array{return get_posts(array('post_type'=>$type,'post_status'=>array('publish','draft','private'),'posts_per_page'=>-1,'orderby'=>'date','order'=>'DESC','meta_key'=>'_pp_project_id','meta_value'=>absint($project_id)));}
 public function find(string $type,int $id){$post=get_post(absint($id));return$post&&$type===$post->post_type?$post:null;}
 public function create(string $type,int $project_id,array $data){$id=wp_insert_post(array('post_type'=>$type,'post_status'=>'publish','post_title'=>sanitize_text_field($data['title']??''),'post_content'=>wp_kses_post($data['content']??''),'post_author'=>get_current_user_id()),true);if(is_wp_error($id))return$id;update_post_meta($id,'_pp_project_id',absint($project_id));if('pp_discussion'===$type)update_post_meta($id,'_pp_discussion_status','open');return get_post($id);}
 public function delete(string $type,int $project_id,int $id):bool{$post=$this->find($type,$id);return$post&&absint(get_post_meta($id,'_pp_project_id',true))===absint($project_id)&&false!==wp_delete_post($id,true);}
 public function comments(int $discussion_id):array{return get_comments(array('post_id'=>absint($discussion_id),'status'=>'approve','order'=>'ASC'));}
 public function add_comment(int $project_id,int $discussion_id,string $content){$post=$this->find('pp_discussion',$discussion_id);if(!$post||absint(get_post_meta($discussion_id,'_pp_project_id',true))!==absint($project_id))return false;$user=wp_get_current_user();return wp_insert_comment(array('comment_post_ID'=>$discussion_id,'comment_content'=>wp_kses_post($content),'user_id'=>$user->ID,'comment_author'=>$user->display_name,'comment_author_email'=>$user->user_email,'comment_approved'=>1));}
}
