<select id="{$option->optionName}" name="values[{$option->optionName}]">
	{foreach from=$validFormats item=validFormat}
		<option value="{$validFormat}" {if $validFormat == $value} selected{/if}>
			{$validFormat|ucfirst}
		</option>
	{/foreach}
</select>
