# AutoLaTeX

[README in English](https://github.com/Izumiko/AutoLaTeX/blob/master/README_en.md)

### 简介

自动渲染 LaTeX 公式。

可选的渲染方式：

- KaTeX（默认）
- MathJax

可选的脚本提供方式：

- 本地（默认）
- jsDelivr CDN
- CDNJS CDN

当不使用本地脚本时，可以选择不上传KaTeX和MathJax的资源文件。

### 插件信息

原作者：[bLue](https://dreamer.blue)
修改者：[Izumiko](https://github.com/Izumiko)

版本：0.2.0

语言支持：简体中文

更新日期：2023-07-08

GitHub：[https://github.com/Izumiko/AutoLaTeX](https://github.com/Izumiko/AutoLaTeX)

### 使用方法

1. 建议从 [releases](https://github.com/Izumiko/AutoLaTeX/releases) 下载插件，解压后将插件目录重命名为 `AutoLaTeX` 并复制到 Typecho 的 `usr/plugins/` 目录下。如已存在之前的版本，请删除后再执行复制
2. 在 Typecho 后台中启用插件。如需切换渲染方式请到 Typecho 后台插件设置中修改

**提示**：由于 LaTeX 渲染相关资源文件数量较大，建议在服务器端进行解压。

### 开源协议

[GPL-3.0](https://github.com/Izumiko/AutoLaTeX/blob/master/LICENSE)

