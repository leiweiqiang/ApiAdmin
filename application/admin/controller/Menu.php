<?php
/**
 * 目录管理
 * @since   2018-01-16
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;

use app\model\AdminMenu;
use app\util\ReturnCode;
use app\util\Tools;

class Menu extends Base {

    /**
     * 获取菜单列表
     * @return array
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index() {
        $origin = (new AdminMenu)->order('sort', 'ASC')->select();
        $origin = Tools::buildArrFromObj($origin);
        $list = Tools::listToTree($origin);
        $choose = Tools::formatTree($list);
        return $this->buildSuccess([
            'list'   => $list,
            'choose' => $choose
        ]);
    }

    /**
     * 新增菜单
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function add() {
        $postData = $this->request->post();
        if ($postData['url']) {
            $postData['url'] = 'admin/' . $postData['url'];
        }
        $res = AdminMenu::create($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        } else {
            return $this->buildSuccess();
        }
    }

    /**
     * 菜单状态编辑
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = AdminMenu::update([
            'id'   => $id,
            'hide' => $status
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 编辑菜单
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edit() {
        $postData = $this->request->post();
        if ($postData['url']) {
            $postData['url'] = 'admin/' . $postData['url'];
        }
        $res = AdminMenu::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    /**
     * 删除菜单
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function del() {
        $id = $this->request->get('id');
        if (!$id) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        $childNum = AdminMenu::where(['fid' => $id])->count();
        if ($childNum) {
            return $this->buildFailed(ReturnCode::INVALID, '当前菜单存在子菜单,不可以被删除!');
        }
        AdminMenu::destroy($id);

        return $this->buildSuccess();
    }
}
