{include file='header' pageTitle='wcf.acp.customOption.list'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.customOption.list{/lang}</h1>
	</div>
</header>

{if $objects|count}
	<div id="optionList" class="section tabularBox sortableListContainer">
		<table class="table jsObjectActionContainer" data-object-action-class-name="assets\data\option\AssetOptionAction">
			<thead>
				<tr>
					<th class="columnID columnOptionID{if $sortField == 'optionID'} active {$sortOrder}{/if}" colspan="2"><a href="{link controller='AssetOptionList' application='assets'}pageNo={@$pageNo}&sortField=optionID&sortOrder={if $sortField == 'optionID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnTitle columnOptionTitle{if $sortField == 'optionTitle'} active {$sortOrder}{/if}"><a href="{link controller='AssetOptionList' application='assets'}pageNo={@$pageNo}&sortField=optionTitle&sortOrder={if $sortField == 'optionTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.name{/lang}</a></th>
					<th class="columnText columnOptionType{if $sortField == 'optionType'} active {$sortOrder}{/if}"><a href="{link controller='AssetOptionList' application='assets'}pageNo={@$pageNo}&sortField=optionType&sortOrder={if $sortField == 'optionType' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.customOption.optionType{/lang}</a></th>
					<th class="columnDigits columnShowOrder{if $sortField == 'showOrder'} active {$sortOrder}{/if}"><a href="{link controller='AssetOptionList' application='assets'}pageNo={@$pageNo}&sortField=showOrder&sortOrder={if $sortField == 'showOrder' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.showOrder{/lang}</a></th>
					
					{event name='columnHeads'}
				</tr>
			</thead>
			
			<tbody class="sortableList jsReloadPageWhenEmpty" data-object-id="0">
				{foreach from=$objects item=option}
					<tr class="sortableNode jsOptionRow jsObjectActionObject" data-object-id="{$option->getObjectID()}">
						<td class="columnIcon">
							{objectAction action="toggle" isDisabled=$option->isDisabled}
							<a href="{link controller='AssetOptionEdit' application='assets' id=$option->optionID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">{icon name='pencil'}</a>
							{objectAction action="delete" objectTitle=$option->getTitle()}
							
							{event name='rowButtons'}
						</td>
						<td class="columnID">{$option->optionID}</td>
						<td class="columnTitle columnoptionTitle"><a href="{link controller='AssetOptionEdit' application='assets' id=$option->optionID}{/link}">{$option->getTitle()}</a></td>
						<td class="columnText columnOptionType">{lang}wcf.acp.customOption.optionType.{$option->optionType}{/lang}</td>
						<td class="columnDigits columnShowOrder">{#$option->showOrder}</td>
						
						{event name='columns'}
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
	
	<div class="formSubmit">
		<button type="button" class="button buttonPrimary" data-type="submit">{lang}wcf.global.button.saveSorting{/lang}</button>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

<script data-relocate="true">
	require(['WoltLabSuite/Core/Ui/Sortable/List'], (UiSortableList) => {
		new UiSortableList({
			containerId: 'optionList',
			className: 'assets\\data\\option\\AssetOptionAction',
			isSimpleSorting: true,
		});
	});
</script>

{include file='footer'}
