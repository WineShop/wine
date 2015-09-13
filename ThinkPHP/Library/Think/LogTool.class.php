<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 15-9-13
 * Time: 上午11:13
 */
namespace Think;
/**
 * 日志处理类
 */
class LogTool
{

    /**
     * @var LogService
     */
    private static $self = NULL;

    const PAGE_LIMIT = 10;

    static public function instance()
    {
        if (is_null(self::$self)) {
            self::$self = new self();
        }
        self::setLogPath();
        return self::$self;
    }

    public function Log()
    {
        $Monolog = Log::getMonolog();
        $Monolog->addError();
    }

    /**
     * 设置路径
     * @throws \Exception
     */
    public static function setLogPath(){
        if(!class_exists('Seaslog')){
            throw new \Exception('SeasLog没有开启,请开启服务！');
        }
        //设置seasLog保存位置
        \SeasLog::setBasePath(C('SEASLOG_SAVEA_PATH'));

    }

    /**
     * 获取保存路径
     * @return mixed
     */
    public static function  getlogPath()
    {
        self::setLogPath();
        return \SeasLog::getBasePath();
    }

    /**
     * @param int $page
     * @param null $iLogType
     * @param null $time
     * @param null $level
     * @param null $key_word
     *
     * @return array
     */
    public function getLog($page = 0, $iLogType = NULL, $time = NULL, $level = NULL, $key_word = NULL)
    {
        $return = array(
            'page' => array(),
            'data' => array()
        );

        \Seaslog::setLogger($iLogType);
        $date = date('Ymd', time());
        if ($time) {
            $time = substr($time, 0, 8);
            $date = $time;
        }
        $data      = array();
        $page_info = array();
        if ($level === NULL) {
            $logList = \SeasLog::analyzerDetail($level, $date, $key_word, 1, 50);
        } else {
            $rowCount              = \SeasLog::analyzerCount($level, $date, $key_word);
            $page_info['max_page'] = ceil($rowCount / self::PAGE_LIMIT);
            if ($page > $page_info['max_page']) {
                $page = $page_info['max_page'];
            }
            if ($page < 1) {
                $page = 1;
            }
            if ($page_info['max_page'] <= 9) {
                $return['jump'] = array(
                    'min' => 1,
                    'max' => $page_info['max_page']
                );
            } elseif ($page > 5 && $page < $page_info['max_page'] - 5) {
                $return['jump'] = array(
                    'min' => $page - 4,
                    'max' => $page + 4
                );
            } elseif ($page < 5) {
                $return['jump'] = array(
                    'min' => 1,
                    'max' => 9
                );
            } else {
                $return['jump'] = array(
                    'min' => $page_info['max_page'] - 8,
                    'max' => $page_info['max_page']
                );
            }

            $page_info['current_page'] = $page;

            $start = $rowCount - self::PAGE_LIMIT * $page + 1;
            $end   = $rowCount - self::PAGE_LIMIT * $page + self::PAGE_LIMIT;
            if ($start < 0) {
                $end   = $rowCount - self::PAGE_LIMIT * $page + self::PAGE_LIMIT;
                $start = 1;
            }
            $logList = \SeasLog::analyzerDetail($level, $date, $key_word, $start, $end);
        }

        $i = 0;
        if (count($logList)) {
            foreach ($logList as $info) {
                if ($info == '::::::::::::::') {
                    continue;
                }
                $tmp = explode('|', $info);
                if (count($tmp) != 5) {
                    if (!count($data)) {
                        continue;
                    } else {
                        $data[$i - 1]['detail'] .= $info;
                        continue;
                    }
                }
                $data[$i]['level']  = trim($tmp['0']);
                $data[$i]['pid']    = trim($tmp['1']);
                $time               = trim($tmp['3']);
                $data[$i]['time']   = $time;
                $data[$i]['detail'] = trim($tmp['4']);
                $i++;
            }
            krsort($data);
            $return['data'] = $data;
        }
        $return['page'] = $page_info;

        return $return;
    }

    /**
     *  $txt,$jsondata二者不能同时为空；
     * $txt可以为空,可以有中文，如果不为空则不能含有' { ',$jsondata可以为空数组、一维数组及多维数组，且非空数组必须为关联数组，不能含有中文
     */
    public function setLog($level, $txt = '', $jsondata = array(), array $content = array(), $module = '')
    {
        if (!empty($jsondata)) {

            $jsondata = $txt . (json_encode($jsondata));

        } else {
            $jsondata = $txt;
        }
        if ($module) {
            \SeasLog::$level($jsondata, $content, $module);
        } else {
            \SeasLog::$level($jsondata, $content);
        }
    }

    public function setLogger($module)
    {
        \SeasLog::setLogger($module);
    }
}