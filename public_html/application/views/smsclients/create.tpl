{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        <div class="row">
            <div class="col-sm-4 col-sm-offset-4">
                {include file="global/notification.tpl"}
            </div>
        </div>
        <form method="post" action="">
            <div class="row">
                <div class="col-sm-4 col-sm-offset-4">
                    <div class="form-group">
                        <label for="inputName">Name</label>
                        <input type="text" name="name" class="form-control" id="inputName" placeholder="Name">
                    </div>
                    <div class="form-group">
                        <label for="inputNumber">Phone number</label>
                        <input type="text" name="number" class="form-control" id="inputNumber" placeholder="+44123456789">
                    </div>
                    <div class="form-group">
                        <label for="inputLocation">Location</label>
                        <input type="text" name="location" class="form-control" id="inputLocation" placeholder="Tanzania">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-lg btn-primary">Add</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
{/block}
