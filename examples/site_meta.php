<?php

/**
 * 站点元信息管理模块
 * 用于集中管理站点基础信息并提供描述生成
 */

class SiteMeta
{
    /**
     * @var array 站点元数据存储
     */
    private array $metaData = [];

    /**
     * @var string 站点主域名
     */
    private string $baseUrl;

    /**
     * 构造函数
     *
     * @param string $baseUrl 站点基础URL
     * @param array  $initialData 初始元数据
     */
    public function __construct(string $baseUrl = '', array $initialData = [])
    {
        $this->baseUrl = $baseUrl ?: 'https://h5-web-leyu.com.cn';
        $this->metaData = $initialData ?: $this->loadDefaultMeta();
    }

    /**
     * 加载默认站点元信息
     *
     * @return array
     */
    private function loadDefaultMeta(): array
    {
        return [
            'site_name'        => '乐鱼体育',
            'site_keywords'    => ['乐鱼体育', '体育赛事', '在线娱乐'],
            'site_description' => '乐鱼体育为您提供丰富的体育赛事资讯与服务',
            'site_url'         => $this->baseUrl,
            'site_language'    => 'zh-CN',
            'site_version'     => '2.1.3',
            'author'           => '乐鱼技术团队',
            'created_date'     => '2024-03-15',
            'update_date'      => date('Y-m-d'),
            'contact_email'    => 'support@leyu-sports.com',
            'meta_tags'        => [
                'og:title'       => '乐鱼体育 - 精彩赛事尽在掌握',
                'og:description' => '获取最新体育赛事信息，尽在乐鱼体育平台。',
                'og:url'         => $this->baseUrl,
                'og:type'        => 'website',
                'twitter:card'   => 'summary_large_image',
            ],
            'features'         => [
                'live_score'   => true,
                'news_feed'    => true,
                'user_center'  => false,
                'multi_lang'   => true,
            ],
            'analytics_code'   => '',
            'robots_policy'    => 'index, follow',
        ];
    }

    /**
     * 获取指定元数据值
     *
     * @param string $key 键名
     * @param mixed  $default 默认值
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->metaData[$key] ?? $default;
    }

    /**
     * 设置元数据
     *
     * @param string $key 键名
     * @param mixed  $value 值
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $this->metaData[$key] = $value;
    }

    /**
     * 生成站点的简短描述文本
     * 用于SEO meta description或社交媒体摘要
     *
     * @param int $maxLength 最大字符长度，默认150
     * @return string
     */
    public function generateShortDescription(int $maxLength = 150): string
    {
        $parts = [];

        $name = $this->get('site_name', '');
        if ($name) {
            $parts[] = $name;
        }

        $keywords = $this->get('site_keywords', []);
        if (!empty($keywords)) {
            $keywordStr = implode('、', array_slice($keywords, 0, 3));
            $parts[] = '关键词：' . $keywordStr;
        }

        $desc = $this->get('site_description', '');
        if ($desc) {
            $parts[] = $desc;
        }

        $url = $this->get('site_url', '');
        if ($url) {
            $parts[] = '官网：' . $url;
        }

        $fullText = implode(' | ', $parts);

        // 限制长度，避免截断时产生乱码
        if (mb_strlen($fullText) > $maxLength) {
            $fullText = mb_substr($fullText, 0, $maxLength - 3) . '...';
        }

        return $fullText;
    }

    /**
     * 生成HTML meta标签块
     *
     * @return string
     */
    public function renderMetaTags(): string
    {
        $html = '';

        $tags = $this->get('meta_tags', []);
        foreach ($tags as $property => $content) {
            $escapedContent = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
            if (str_starts_with($property, 'og:')) {
                $html .= '<meta property="' . htmlspecialchars($property, ENT_QUOTES, 'UTF-8') . '" content="' . $escapedContent . '" />' . "\n";
            } elseif (str_starts_with($property, 'twitter:')) {
                $html .= '<meta name="' . htmlspecialchars($property, ENT_QUOTES, 'UTF-8') . '" content="' . $escapedContent . '" />' . "\n";
            }
        }

        // 基础meta
        $description = htmlspecialchars($this->generateShortDescription(160), ENT_QUOTES, 'UTF-8');
        $keywordsStr = htmlspecialchars(implode(',', $this->get('site_keywords', [])), ENT_QUOTES, 'UTF-8');
        $html .= '<meta name="description" content="' . $description . '" />' . "\n";
        $html .= '<meta name="keywords" content="' . $keywordsStr . '" />' . "\n";

        return $html;
    }

    /**
     * 获取完整元数据数组
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->metaData;
    }

    /**
     * 重置为默认元数据
     *
     * @return void
     */
    public function resetToDefault(): void
    {
        $this->metaData = $this->loadDefaultMeta();
    }
}

// 示例用法
$meta = new SiteMeta();
echo $meta->generateShortDescription(120) . "\n";
echo $meta->renderMetaTags();