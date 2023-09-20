<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>{lang}wcf.label.title{/lang}</title>
		<style>
.page {
    font-family: arial, helvetica, sans-serif;
    width: {@$pageWidth}pt;
    height: {@$pageHeight}pt;
    margin-top: {ASSETS_LABEL_PAGE_MARGIN_TOP}cm;
    margin-right: {ASSETS_LABEL_PAGE_MARGIN_RIGHT}cm;
    margin-bottom: {ASSETS_LABEL_PAGE_MARGIN_BOTTOM}cm;
    margin-left: {ASSETS_LABEL_PAGE_MARGIN_LEFT}cm;
    font-family: {$fontFamily};
    font-size: {ASSETS_LABEL_FONT_SIZE}cm;
    display: grid;
    grid-template-columns: auto auto auto;
    grid-row-gap: {ASSETS_LABEL_ROW_GAP}cm;
    grid-column-gap: {ASSETS_LABEL_COLUMN_GAP}cm;
    align-items: center;
    justify-items: center;
}
.label {
    width: {ASSETS_LABEL_WIDTH}cm;
    height: {ASSETS_LABEL_HEIGHT}cm;
    padding: {ASSETS_LABEL_PADDING}cm;
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
		{foreach from=$chunks item=chunk}
			<div class="page">
				{if $skipFields|isset}
					{@$skipFields}
				{/if}
				{foreach from=$chunk item=object}
					<div class="label">
						{if !$object|is_string}
							<div class="rq_img">
								{@$object->getQRCode()}
							</div>
							<div class="qr_text">
								<p><strong>{ASSETS_LABEL_HEADER}</strong></p>
								<p>{lang}wcf.label.asset.title{/lang}</p>
								<p>{lang}wcf.label.asset.category{/lang}</p>
								<p>{lang}wcf.label.asset.location{/lang}</p>
								<p>{lang}wcf.label.asset.objectID{/lang}</p>
							</div>
						{/if}
					</div>
				{/foreach}
			</div>
			<br />
		{/foreach}
	</body>
</html>