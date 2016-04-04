<?php
namespace Nebo15\LumenApplicationable;

class Response extends \Illuminate\Http\Response
{
    public function json($content = [], $code = self::HTTP_OK, $meta = [], $paginate = [], $sandboxData = [])
    {
        $meta['code'] = $code;
        $respond = [
            'meta' => $meta,
            'data' => $content,
        ];
        if (!empty($paginate)) {
            $respond['paging'] = $paginate;
        }
        if (!empty($sandboxData)) {
            $respond['sandbox'] = $sandboxData;
        }
        return $this->setStatusCode($code)->setContent($respond);
    }
}
