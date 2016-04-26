<div class="form-group">
	<label for="groups" class="control-label col-sm-3">Group(s)</label>
	<div class="col-sm-6">
		<ul class="list-unstyled">
			{foreach $groups as $group}
				{$checked = ""}
				{if in_array($group, $usergroups)}
					{$checked = "checked='checked'"}
				{/if}
				<li>
					<label><input type="checkbox" value="{$group->id}" name="user_groups[]" {$checked}> {$group->name}</label>
				</li>
			{/foreach}
		</ul>
	</div>
</div>
