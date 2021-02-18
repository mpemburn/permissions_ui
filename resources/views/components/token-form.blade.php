<form id="modal_form" action="{!! $action !!}">
    @csrf
    <input type="hidden" name="b_token" value="{!! $token !!}">
</form>
