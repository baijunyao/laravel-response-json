<?php

if (! function_exists('response_json')) {
    /**
     * ajax返回数据 如果是200则$data是要返回的数据
     * 如果不是200 则$data 为错误的提示文字
     *
     * @param string $data 需要返回的数据
     * @param int $status_code
     * @return \Illuminate\Http\JsonResponse
     */
    function response_json($status_code = 200, $data = '')
    {
        //如果如果是错误 返回错误信息
        if ($status_code != 200 || is_string($data)) {
            //增加status_code
            $data = ['status_code' => $status_code, 'message' => $data,];
            return response()->json($data, $status_code);
        }
        //如果是对象 先转成数组
        if (is_object($data)) {
            $data = $data->toArray();
        }

        if (! function_exists('toString')) {
            /**
             * 将数组递归转字符串
             * @param  array $arr 需要转的数组
             * @return array       转换后的数组
             */
            function toString($arr)
            {
                // 禁止使用和为了统一字段做的判断
                $reserved_words = array('id', 'title', 'description');
                foreach ($arr as $k => $v) {
                    //如果是对象先转数组
                    if (is_object($v)) {
                        $v = $v->toArray();
                    }
                    //如果是数组；则递归转字符串
                    if (is_array($v)) {
                        $arr[$k] = toString($v);
                    } else {
                        //判断是否有禁止使用的字段
                        in_array($k, $reserved_words, true) && die('禁止使用【' . $k . '】这个键名 —— 此提示是laravel-response-json扩展包中的response_json函数返回的');
                        //转成字符串类型
                        $arr[$k] = strval($v);
                    }
                }
                return $arr;
            }
        }

        //判断是否有返回的数据
        if (is_array($data)) {
            //先把所有字段都转成字符串类型
            $data = toString($data);
        }
        return response()->json($data, $status_code);
    }
}
