<?php
/**
 * 自动渲染 LaTeX 公式
 * 
 * @package AutoLaTeX 
 * @author bLue
 * @version 0.2.0
 * @link https://dreamer.blue
 */
class AutoLaTeX_Plugin implements Typecho_Plugin_Interface {
     /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate() {
        Typecho_Plugin::factory('Widget_Archive')->header = array(__CLASS__, 'header');
        Typecho_Plugin::factory('Widget_Archive')->footer = array(__CLASS__, 'footer');
        Typecho_Plugin::factory('admin/write-post.php')->content = array(__CLASS__, 'header');
        Typecho_Plugin::factory('admin/write-post.php')->bottom = array(__CLASS__, 'footer');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate() {}

    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form) {
        $renderingList = array(
            'KaTeX' => 'KaTeX',
            'MathJax' => 'MathJax',
        );
        $name = new Typecho_Widget_Helper_Form_Element_Select('rendering', $renderingList, 'KaTeX', _t('选择 LaTeX 渲染方式'));
        $form->addInput($name->addRule('enum', _t('请选择 LaTeX 渲染方式'), $renderingList));

        $provideTypeList = array(
            'local' => '本地',
            'jsDelivr' => 'jsDelivr',
            'cdnjs' => 'cdnjs',
        );
        $provide = new Typecho_Widget_Helper_Form_Element_Select('provide', $provideTypeList, 'local', _t('选择LaTeX脚本提供方式'));
        $form->addInput($provide->addRule('enum', _t('请选择脚本提供方式'), $provideTypeList));
    }

    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form) {}

    /**
     * 插件实现方法
     * 
     * @access public
     * @return void
     */
    public static function render() {}

    /**
     * 添加额外输出到 Header
     * 
     * @access public
     * @return void
     */
    public static function header() {
        $pluginDir = Helper::options()->pluginUrl . '/AutoLaTeX';
        $rendering = Helper::options()->plugin('AutoLaTeX')->rendering;
        $provide = Helper::options()->plugin('AutoLaTeX')->provide;
        $url = "$pluginDir/KaTeX";
        switch ($provide) {
            case 'local':
                $url = "$pluginDir/KaTeX";
                break;
            case 'jsDelivr':
                $url = 'https://cdn.jsdelivr.net/npm/katex@0.16.8/dist';
                break;
            case 'cdnjs':
                $url = 'https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.8';
                break;
        }
        switch($rendering) {
            case 'MathJax':
                break;
            case 'KaTeX':
                echo <<<HTML
                    <link href="$url/katex.min.css" rel="stylesheet">
HTML;
                break;
        }
    }

    /**
     * 添加额外输出到 Footer
     * 
     * @access public
     * @return void
     */
    public static function footer() {
        $pluginDir = Helper::options()->pluginUrl . '/AutoLaTeX';
        $rendering = Helper::options()->plugin('AutoLaTeX')->rendering;
        $provide = Helper::options()->plugin('AutoLaTeX')->provide;
        $url = "$pluginDir/$rendering";
        switch ($provide) {
            case 'local':
                $url = "$pluginDir/$rendering";
                break;
            case 'jsDelivr':
                switch ($rendering) {
                    case 'MathJax':
                        $url = 'https://cdn.jsdelivr.net/npm/mathjax@3.2.2/es5';
                        break;
                    case 'KaTeX':
                        $url = 'https://cdn.jsdelivr.net/npm/katex@0.16.8/dist';
                        break;
                }
                break;
            case 'cdnjs':
                switch ($rendering) {
                    case 'MathJax':
                        $url = 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/3.2.2/es5';
                        break;
                    case 'KaTeX':
                        $url = 'https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.8';
                        break;
                }
                break;
        }
        switch($rendering) {
            case 'MathJax':
                echo <<<HTML
                    <script id="MathJax-script" async src="$url/tex-mml-chtml.js"></script>
                    <script>
                        function typeset(element) {
                            MathJax.startup.promise = MathJax.startup.promise
                                .then(() => MathJax.typesetPromise(element))
                                .catch((err) => console.log('Typeset failed: ' + err.message));
                            return MathJax.startup.promise;
                        }
                        async function triggerRenderingLaTeX(element) {
                            await typeset([element]);
                        }
                    </script>
HTML;
                break;
            case 'KaTeX':
                echo <<<HTML
                    <script defer src="$url/katex.min.js"></script>
                    <script defer src="$url/contrib/auto-render.min.js"></script>
                    <script>
                        function triggerRenderingLaTeX(element) {
                            renderMathInElement(
                                element,
                                {
                                    delimiters: [
                                        {left: "$$", right: "$$", display: true},
                                        {left: "\\\\[", right: "\\\\]", display: true},
                                        {left: "$", right: "$", display: false},
                                        {left: "\\\\(", right: "\\\\)", display: false}
                                    ]
                                }
                            );
                        }

                        document.addEventListener("DOMContentLoaded", function() {
                            triggerRenderingLaTeX(document.body);
                        });
                    </script>
HTML;
                break;
        }
        echo <<<HTML
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const wmdPreviewLink = document.querySelector("a[href='#wmd-preview']");
                    const wmdPreviewContainer = document.querySelector("#wmd-preview");
                    if(wmdPreviewLink && wmdPreviewContainer) {
                        wmdPreviewLink.onclick = function() {
                            triggerRenderingLaTeX(wmdPreviewContainer);
                        };
                    }
                });
            </script>
HTML;
    }
}
