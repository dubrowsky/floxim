<form class="panel-body" fx:template="authform_popup" fx:of="widget_authform.show"  method="POST" action="/floxim/" >
    <input type="hidden" name="essence" value="module_auth" />
    <input type="hidden" name="action" value="auth" />
    <div class="input-group">
        <span class="input-group-addon">@</span>
        <input type="text" name="AUTH_USER" class="form-control" placeholder="Username">
    </div>
    <div class="input-group">
        <span class="input-group-addon">*</span>
        <input type="password" name="AUTH_PW" class="form-control" placeholder="Password">
    </div>
    <div class="input-group">
        <button type="submit" class="btn btn-primary">Log In</button>
    </div>
</form>