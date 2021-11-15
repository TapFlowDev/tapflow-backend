<!DOCTYPE html>
<html>

<body>
    <form action="/api/addCompany" method="POST" enctype="multipart/form-data">
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

    </form>
</body>

</html>
