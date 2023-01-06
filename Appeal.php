<?php
/**
 * models/Appeal.php
 * ============================================================
 * Copyright (c) 2018-2200 (https://www.xi5jie.com)
 * ------------------------------------------------------------
 * This is not a free software, 
 * without any authorization is not allowed to use and spread.
 * ============================================================
 * @description: 申诉模型
 * @author: xiejianwen <xiejianwen@inmyshow.com>
 * @date: 2019年09月16日 下午3:45:45
 * @version: v1.0.0
 */
use Basic\ModelBasic;

class AppealModel extends ModelBasic
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * @desc 获取西红试商品的处罚记录
     */
    public function getXhsXinyongAppeals($uid = 0, $type = 0, $r_id = 0)
    {
        if (!$uid || !$type || !$r_id) return false;
        
        $where = [
            'uid'   => $uid,
            'p_type'  => $type,
            'r_id'  => $r_id
        ];
        //var_dump($where);exit;
        return $this->_table("llp_xinyong_record")->_where($where)->_selectOne();
    }
    
    /**
     * desc 通过订单号查询惩罚结束时间
     * @param number $orderid
     * @return string
     */
    public function getAppealsPunishTimeByOrderid($orderid = 0)
    {
        if (!$orderid) return '';
        
        $where = [
            'orderid'   => $orderid
        ];
        //var_dump($where);exit;
        $res = $this->_table("llp_xinyong_record")->_where($where)->_selectOne();
        if(!$res) return '';
        
        //已解封
        //if($res['p_status'] == 1) return '';
        
        $rule = (new ConfigModel())->getXinyong($res['p_type']);
        if(isset($rule['day']) && $rule['day']){
            return date('Y-m-d H:i:s', strtotime("+".$rule['day']." day", strtotime($res['p_time'])));
        }
        return '';
    }
    
    
    /**
     * desc 通过处罚ID和用户ID查询惩罚结束时间
     * @param number $orderid
     * @return string
     */
    public function getAppealsPubishTimeBySpid($uid = 0, $type = 0)
    {
        if (!$uid || !$type) return '';
        
        $where = [
            'uid'       => $uid,
            'p_type'    => $type,
            'p_status'  => 0
        ];
        
        $res = $this->_table("llp_xinyong_record")->_where($where)->_order(['p_time' => 'desc'])->_selectOne();
        if(!$res) return '';
        
        //已解封
        //if($res['p_status'] == 1) return '';
        
        $rule = (new ConfigModel())->getXinyong($res['p_type']);
        if(isset($rule['day']) && $rule['day']){
            return date('Y-m-d H:i:s', strtotime("+".$rule['day']." day", strtotime($res['p_time'])));
        }
        return '';
    }
    
    
    public function getXinyongAppeals($uid = 0, $type = 0)
    {
        if (!$uid || !$type) return false;
        
        $where = [
            'uid'   => $uid,
            'p_type'  => $type,
        ];
        //var_dump($where);exit;
        return $this->_table("llp_xinyong_record")->_where($where)->_selectOne();
    }
    
    
    /**
     * @desc 获取用户的处罚记录
     * @param number $uid
     * @param number $type
     * @param number $appealstatus
     * @return boolean|boolean|unknown
     */
    public function getXinyongAppealsStatus($uid = 0, $type = 0, $appealstatus = 0){
        if (!$uid || !$type) return false;
        
        $where = [
            'uid'    => $uid,
            'p_type' => $type,
            'appealstatus' => $appealstatus
        ];
        
        return $this->_table("llp_xinyong_record")->_where($where)->_selectOne();
    }
    
    /**
     * @desc 根据警告记录id获取内容
     * @param number $id
     * @return boolean|boolean|unknown
     */
    
    public function getXinyongAppeal($id=0)
    {
        
        if (!$id) {
            return false;
        }
        $where = [
            'id'   => $id
        ];
        return $this->_table("llp_xinyong_record")->_where($where)->_selectOne();
     
    }

    /**
     * @desc 根据文章ID获取申诉记录
     * @param number $article_id 文章ID
     * @return boolean|boolean|unknown
     */
    public function getAppealInfo($article_id = 0, $type = 0)
    {
        if (!$article_id) {
            return false;
        }
        $where = [
            'aid'  => $article_id
        ];
        
        if ($type) {
            $where['type'] = $type;
        }
        
        $orderBy = ['id' =>'desc'];
        
        return $this->_table("llp_appeals")->_where($where)->_order($orderBy)->_selectOne();
    }
    
    public function sendAppeal($params = [])
    {
        if (!isset($params['uid']) || !$params['uid'] || !isset($params['aid']) || !$params['aid']) {
            return false;
        }
        $where = [
            'uid'   => $params['uid'],
            'aid'   => $params['aid'],
            'type'  => $params['type']
        ];
        
        $updateData = [
            'appeal_content'    => $params['appeal_content'],
            'appeal_time'       => date('Y-m-d H:i:s'),
            'contact'           => $params['contact'],
            'appeal_status'     => $params['appeal_status']
        ];
        
        if (isset($params['images'])) {
            $updateData['images'] = $params['images'];
        }
        return $this->_table("llp_appeals")->_where($where)->_update($updateData);
    }
    
    /**
     * @desc 修改违规信息
     * @param number $article_id
     * @param array $data
     * @param number $type
     * @return void|boolean
     */
    public function updateReason($article_id = 0, $data = [], $type = 2) {
        if(!$article_id || !$data) return;
        $where = [
            'aid'   => $article_id,
            'type'  => $type
        ];
        return $this->_table('llp_appeals')->_where($where)->_update($data);
    }
    
    //添加申诉信息
    public function addReason($data = []){
        if(!$data) return;
        
        return $this->_table('llp_appeals')->_insert($data);
    }
    
   public function sendXinyongAppeal($params = []) {
	 $where = [
            'id'   => $params['id'],
        ];
	  $updateData = [
            'appealcontent'    => (string)$params['appealcontent'],
            'appealtime'       => date('Y-m-d H:i:s'),
            'appealimage'      => $params['images'],
            'appealphone'      => $params['contact'],
	        'appealstatus'     =>1
        ];
	return  $this->_table("llp_xinyong_record")->_where($where)->_update($updateData); 

   }
   
   /**
    * @desc 根据文章id获取文章的限流信息
    * @param 文章id数组
    */
   public function getAppealList($articles = [])
   {
       if (!$articles || !is_array($articles)) return [];
       
       $articleString = implode(",", $articles);
       $sql = "select aid,appeal_status,type from llp_appeals where `aid` in (".$articleString.") and appeal_status != 1";
       return $this->_query($sql);
   }

   /**
    * @desc 文章根据下架或者限流分类展示给用户的文案
    * @param $type 操作类型 2下架 3 限流
    * @param $offcat 分类
    */
   public function punishArticleTips($type = 2, $offcat = 0)
   {
       //1禁止品类  2推广营销  3版权  4低俗  5政治反动  6色情  7违法违规  8暴恐  9群体 10境外 11未成年 12其他 13涉警
       $configArr = [
           '2' => [
               '0'  => '笔记内容疑似含有违规或敏感信息，现已被下架，请耐心等待人工审核或点击“我要申诉”进行申诉。',
               '1'  => '笔记内容含有禁止品类、高仿假货、烟草、医药、网赚兼职、高风险类目等信息，现已被下架，为了您的内容可以正常展示，请及时修改笔记哦~',
               '2'  => '笔记内容含有交易销售、个人二维码、非官方群二维码等信息，现已被下架，为了您的内容可以正常展示，请及时修改笔记哦~',
               '3'  => '笔记内容含有未经授权或未标明出处的非原创内容等，现已被下架，为了您的内容可以正常展示，请遵守社区规范及时修改（温馨提示：涉及侵权类笔记，如未改正或直接删除笔记都将对账号权益有相关影响）',
               '4'  => '笔记内容含有低俗或性挑逗明显等信息，现已被下架，为了您的内容可以正常展示，请及时修改笔记哦~',
               '5'  => '笔记内容含有涉政敏感等信息，现已被下架，为了您的内容可以正常展示，请及时修改笔记哦~',
               '6'  => '笔记内容含有色情等有害信息，现已被下架，为了您的内容可以正常展示，请及时修改笔记哦~',
               '7'  => '笔记内容含有违禁品、不当表述等信息，现已被下架，为了您的内容可以正常展示，请及时修改笔记哦~',
               '8'  => '笔记内容含有恐怖暴力等信息，现已被下架，为了您的内容可以正常展示，请及时修改笔记哦~',
               '9'  => '笔记内容含有群体游行示威等信息，现已被下架，为了您的内容可以正常展示，请及时修改笔记哦~',
               '10' => '笔记内容含有不宜展示的敏感信息，现已被下架，为了您的内容可以正常展示，请及时修改笔记哦~',
               '11' => '笔记内容含有不利于未成年人身心健康信息，现已被下架，为了您的内容可以正常展示，请及时修改笔记哦~',
               '12' => '笔记内容含有违规信息，现已被下架，为了您的内容可以正常展示，请及时修改笔记哦~',
               '13' => '笔记内容含有涉警敏感等信息，现已被下架，为了您的内容可以正常展示，请及时修改笔记哦~'
           ],
           '3' => [
               '0' => '经举报查实因含非原创内容，将会限制曝光。（温馨提示：搬运其他博主的原创作品，需要经作者授权，并注明引用作者、来源及非原创声明。如未改正或直接删除笔记都将对账号权益有相关影响）'
           ]
       ];
       
       return $configArr[$type][$offcat];
   }
   
   /**
    * @desc 用户删除笔记时，检查一下该篇笔记是否在申诉中，如果是，则将该篇笔记的申诉状态设置为申诉中
    * @param number $articleId
    */
   public function setArticleAppealFail($articleId = 0) {
       if (!$articleId) return false;
       
       $appealInfo = $this->getAppealInfo($articleId);
       if (!$appealInfo) {
           return false;
       }
       if ($appealInfo['appeal_status'] != 0) {
           return false;
       }
       
       $where = [
            "id"    => $appealInfo['id']
       ];
       $updateData = ['appeal_status' => 2];
       return $this->_table("llp_appeals")->_where($where)->_update($updateData);
   }
}
