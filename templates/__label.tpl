<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>{lang}assets.label.title{/lang}</title>
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
    display: inline-block;
    overflow: hidden;
    position: relative;
}
img.AssetQRCode {
    width: {ASSETS_LABEL_QR_WIDTH}cm;
    height: {ASSETS_LABEL_QR_HEIGHT}cm;

    float: left;
    display: inline-flex;
    padding-right: {ASSETS_LABEL_QR_PADDING_RIGHT}cm;
    padding-top: {ASSETS_LABEL_QR_PADDING_TOP}cm;
}
.qr_text h2 {
	font-size: 1.5em;
    margin-block-start: {ASSETS_LABEL_TITLE_MARGIN_START}cm;
    margin-block-end: {ASSETS_LABEL_TITLE_MARGIN_END}cm;
    margin-inline-start: 0px;
    margin-inline-end: 0px;
    font-weight: bold;
}
.qr_text p {
    display: block;
	margin-block-start: {ASSETS_LABEL_TEXT_MARGIN_START}cm;
    margin-block-end: {ASSETS_LABEL_TEXT_MARGIN_END}cm;
    margin-inline-start: 0px;
    margin-inline-end: 0px;
}
.qr_text {
    padding-top: {ASSETS_LABEL_TEXT_PADDING_TOP}cm;
    padding-right: {ASSETS_LABEL_TEXT_PADDING_RIGHT}cm;
    padding-bottom: {ASSETS_LABEL_TEXT_PADDING_BOTTOM}cm;
    padding-left: {ASSETS_LABEL_TEXT_PADDING_LEFT}cm;
    overflow: hidden !important;
    word-wrap: break-word;
    word-break: break-all;
}
img.label_logo {
    height: {ASSETS_LABEL_LOGO_HEIGHT}cm;

    margin-top: {ASSETS_LABEL_LOGO_MARGIN_TOP}cm;
    margin-right: {ASSETS_LABEL_LOGO_MARGIN_RIGHT}cm;
    position: absolute;
    top: 0;
    right: 0;
}

@media  screen {
    .label {
        outline: 0.01cm black solid; /* outline doesn't occupy space like border does */
    }
}</style>
	</head>
	<body>
		{foreach from=$chunks item=chunk}
			<div class="page">
				{foreach from=$chunk item=object}
					<div class="label">
						{if !$object|is_string}
							<img src="{@$object->getQRCode()}" class="AssetQRCode" title="QRCode">
							<div class="qr_text">
								<h2>{ASSETS_LABEL_HEADER}</h2>
								<p>{lang}assets.label.asset.title{/lang}</p>
								<p>{lang}assets.label.asset.category{/lang}</p>
								<p>{lang}assets.label.asset.location{/lang}</p>
								<p>{lang}assets.label.asset.objectID{/lang}</p>
								{event name='LabelExportValues'}
							</div>
							{if ASSETS_LABEL_LOGO !== null && $logo|isset}
								<img class="label_logo" src="{$logo}"/>
							{/if}
						{/if}
					</div>
				{/foreach}
			</div>
			<br />
		{/foreach}
	</body>
</html>