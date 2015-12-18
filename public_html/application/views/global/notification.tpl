{if $notification !== null}
    <div class="alert alert-{$notification.type} alert-dismissable" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {$notification.message}
    </div>
{/if}
