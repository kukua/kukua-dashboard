{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="pull-left js-iframe">
					<iframe src="{$baseUrl}forecast/content?rain" height="802px" width="802px" frameborder="0">{$content}</iframe>
                </div>
                <div class="pull-right">
                    <small>mm = millimeter</small>
                    <table class="table table-condensed">
                        <tr>
                            <td style="background-color:rgba(000,050,255,0.35);">&nbsp;</td>
                            <td style="background-color:rgba(020,000,200,0.55);">&nbsp;</td>
                            <td style="background-color:rgba(130,000,150,0.65);">&nbsp;</td>
                            <td style="background-color:rgba(150,000,050,0.80);">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>0.2 mm/h</td>
                            <td>1.0 mm/h</td>
                            <td>2.0 mm/h</td>
                            <td>5.0 mm/h</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
{/block}
