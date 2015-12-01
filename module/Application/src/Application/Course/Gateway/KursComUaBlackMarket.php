<?php
/**
 * @package Application
 * @author Nikolay Kovenko <nickk@templatemonster.me>
 * @date 30.11.15
 */

namespace Application\Course\Gateway;

use Sunra\PhpSimple\HtmlDomParser;

/**
 * Черный рынок на сайте kurs.com.ua
 * @package Application\Course\Gateway
 */
class KursComUaBlackMarket extends AbstractGateway
{

    private $sourceUrl = 'http://kurs.com.ua/ajax/comm_widget/1217/now/eur,usd,rub';

    /**
     * @var HtmlDomParser
     */
    private $htmlParser;

    /**
     * @param HtmlDomParser $htmlParser
     */
    public function __construct(HtmlDomParser $htmlParser)
    {
        $this->htmlParser = $htmlParser;
    }

    /**
     * @inheritdoc
     */
    protected function handleGet()
    {
        $html = $this->getContent($this->sourceUrl);
        $json = @json_decode($html);

        if (!$json || !$json->success || !$json->table) {
            throw new \Exception('Unknown server error');
        }

        $html = $this->htmlParser->str_get_html($json->table);
        $tableTrs = $html->find('tbody > tr');
        $find = false;
        $tr = current($tableTrs);

        while ($tr && !$find) {
            $a = $tr->find('td > a', 0);
            if ($a && strtolower($a->innertext) === 'usd') {
                $result = $this->findTdWithLargestValue($tr);
                $find = true;
            }

            $tr = next($tableTrs);
        }

        if ($find && !empty($result)) {
            return [
                'value' => $result,
            ];
        }

        throw new \Exception('USD not found in server response');
    }

    /**
     * Возвращает контент страницы по урлу
     * @param string $url
     * @return string
     */
    private function getContent($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.65 Safari/537.36");
        curl_setopt($ch, CURLOPT_REFERER, "http://kurs.com.ua/lvov");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch); // выполняем запрос curl
        curl_close($ch);

        return $result;
    }

    /**
     * Возвращает максимальное числовое значение в ячейках строки
     * @param \simple_html_dom_node $tr
     * @return float
     */
    private function findTdWithLargestValue(\simple_html_dom_node $tr)
    {
        $floatValues = [];

        foreach ($tr->find('td') as $td) {
            if (preg_match("/^[\s]*([\d.]+)[\s]*$/", $td->innertext, $matches)) {
                $floatValues[] = (float) $matches[1];
            }
        }

        return max($floatValues);
    }
}
