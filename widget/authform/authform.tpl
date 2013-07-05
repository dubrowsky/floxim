<form fx:template="show" method="POST" action="/floxim/">
    <input type="hidden" name="essence" value="module_auth" />
    <input type="hidden" name="action" value="auth" />
    <input type="text" name="AUTH_USER" /> <br />
    <input type="password" name="AUTH_PW" /> <br />
    <input type="submit" value="{%login_button}Login{/%}" />
</form>