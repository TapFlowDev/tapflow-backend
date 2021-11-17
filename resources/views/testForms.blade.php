<!DOCTYPE html>
<html>

<body>
    {{-- <form action="/api/addCompany" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="image">
        <br>
        <input type="file" name="attachment">
        <br>
        <input type="text" name="admin_id" value="5">
        <br>
        <input type="text" name="name" value="name">
        <br>
        <input type="text" name="bio" value="bio">
        <br>
        <input type="text" name="country" value="jo">
        <br>
        <input type="text" name="field" value="IT">
        <br>
        <input type="text" name="employees_number" value="6">
        <br>
         <input type="text" name="link" value="https://www.google.com/">
        <br>
        <button type="submit">submit</button>

    </form> --}}
    <form action="http://207.154.230.96/api/Login" method="POST" enctype="multipart/form-data">
        @csrf
        @method('POST')
        <input type="email" name="email" value="testhamzah1@test.com">
        <br>
        <input type="password" name="password">
        <br>
        <button type="submit">submit</button>

    </form>
</body>
<script>
</script>
</html>
