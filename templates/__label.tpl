<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>{lang}wcf.label.title{/lang}</title>
		<style>
body {
    font-family: arial, helvetica, sans-serif;
    width: {@$pageWidth}cm;
    height: {@$pageHeight}cm;
    margin-top: {ASSETS_LABEL_PAGE_MARGIN_TOP}cm;
    margin-right: {ASSETS_LABEL_PAGE_MARGIN_RIGHT}cm;
    margin-bottom: {ASSETS_LABEL_PAGE_MARGIN_BOTTOM}cm;
    margin-left: {ASSETS_LABEL_PAGE_MARGIN_LEFT}cm;
    font-family: {$fontFamily};
    font-size: {ASSETS_LABEL_FONT_SIZE}cm;
}
.label {
    width: {ASSETS_LABEL_WIDTH}cm;
    height: {ASSETS_LABEL_HEIGHT}cm;
    padding: {ASSETS_LABEL_PADDING}cm;
    margin-right: {ASSETS_LABEL_MARGIN_RIGHT}cm; /* the gutter */
    margin-bottom: {ASSETS_LABEL_MARGIN_BOTTOM}cm;
    display: inline-block;
    overflow: hidden;
}
.page-break  {
    page-break-after:always;
}
div.qr_img, img.AssetQRCode {
    width: {ASSETS_LABEL_QR_WIDTH}cm;
    height: {ASSETS_LABEL_QR_HEIGHT}cm;

	float: left;
    display: inline-flex;
    padding-right: {ASSETS_LABEL_QR_PADDING_RIGHT}cm;
    padding-top: {ASSETS_LABEL_QR_PADDING_TOP}cm;
}
div.label-logo {
    float: right;
    display: inline-block;
    height: {ASSETS_LABEL_LOGO_HEIGHT}cm;
}
.qr_text {
    padding-top: {ASSETS_LABEL_TEXT_PADDING_TOP}cm;
    padding-right: {ASSETS_LABEL_TEXT_PADDING_RIGHT}cm;
    overflow: hidden !important;
    display: inline;
	word-wrap: break-word;
    word-break: break-all;
}
.next-padding {
    margin-top: {ASSETS_LABEL_PAGE_MARGIN_TOP}cm;
    margin-right: {ASSETS_LABEL_PAGE_MARGIN_RIGHT}cm;
    margin-bottom: {ASSETS_LABEL_PAGE_MARGIN_BOTTOM}cm;
    margin-left: {ASSETS_LABEL_PAGE_MARGIN_LEFT}cm;
}
@media  print {
    .noprint {
        display: none !important;
    }
    .next-padding {
		margin-top: {ASSETS_LABEL_PAGE_MARGIN_TOP}cm;
        margin-right: {ASSETS_LABEL_PAGE_MARGIN_RIGHT}cm;
        margin-bottom: {ASSETS_LABEL_PAGE_MARGIN_BOTTOM}cm;
        margin-left: {ASSETS_LABEL_PAGE_MARGIN_LEFT}cm;
        font-size: 0;
    }
}
@media  screen {
    .label {
        outline: {ASSETS_LABEL_OUTLINE}cm black solid; /* outline doesn't occupy space like border does */
    }
    .noprint {
        font-size: 13px;
        padding-bottom: 15px;
    }
}
		</style>
	</head>
	<body>
		{if $skipFields|isset}
			{@$skipFields}
		{/if}
		{foreach from=$objects item=object}
			<div class="label">
				<div class="rq_img">
					{@$object->getQRCode()}
				</div>
				<div class="qr_text">
					<p>{$object->getTitle()}</p>
					<p>{$object->getCategory()->getTitle()}</p>
					<p>{$object->getLocation()->getTitle()}</p>
					<p>{$object->getObjectID()}</p>
				</div>
			</div>
		{/foreach}
	</body>
</html>