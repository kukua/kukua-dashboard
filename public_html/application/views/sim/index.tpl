{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
	        <table class="table">
	 	    <thead>
			<tr>
			    <th></th>
			    <th>Name</th>
			    <th>Country</th>
			    <th>ICCID</th>
			    <th>Last connection time</th>
			    <th>Size of upload</th>
			    <th>Status</th>
			</tr>
	 	    </thead>
	 	    <tbody>
                	{foreach $simcards as $key => $card}
			    <tr>
			    	<td>#{$key}</td>
			        <td>{$card->friendlyName}</td>
			        <td>{$card->group}</td>
			        <td>{$card->ICCID}</td>
			        <td>{$card->LastRadiusStop}</td>
			        <td>{$card->LastRadiusBytes}</td>
			        <td class="bg-{$card->Status}"></td>
			    </tr>
			{/foreach}
	 	    </tbody>
		</table>
            </div>
        </div>
    </div>
{/block}
