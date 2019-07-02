<?php
namespace thinkbuilder\node;

use thinkbuilder\Cache;
use thinkbuilder\generator\Generator;
use thinkbuilder\helper\FileHelper;
use thinkbuilder\helper\TemplateHelper;

/**
 * Class Application
 * @package thinkbuilder\node
 */
class Application extends Node
{
    //应用的根命名空间
    protected $namespace = '';
    //入口文件名称，不需要输入 .php 后缀
    protected $portal = 'index';
    //模块列表
    protected $modules = [];
    protected $dbEngine = 'MyISAM';

    public function process()
    {
        //创建目录
        FileHelper::mkdir($this->path);

        //创建入口文件
        $config = Cache::getInstance()->get('config');
        if ($config['actions']['portal']) {
            Generator::create('php\\Portal', [
                'path' => Cache::getInstance()->get('paths')['public'],
                'file_name' => $this->portal . '.php',
                'template' => TemplateHelper::fetchTemplate('portal'),
                'data' => $this->data
            ])->generate()->writeToFile();
        }

        if ($config['actions']['copy']) {
            //拷贝应用文件
            FileHelper::copyFiles(ASSETS_PATH . '/thinkphp/app', $this->path);
        }

        //设置应用根命名空间
        Cache::getInstance()->set('root_name_space', $this->namespace);
        //设置数据库引擎
        Cache::getInstance()->set($this->namespace.'_dbEngine', $this->dbEngine);
        $this->processChildren('module');
    }

    public function setNameSpace()
    {
        $this->data['namespace'] = $this->namespace;
    }
}