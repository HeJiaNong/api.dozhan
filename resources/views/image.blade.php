
<form method="post" action="{{ route('upload.image') }}" enctype="multipart/form-data">
    {{ csrf_field() }}


    <h1>上传图片👇</h1>

    <input name="file" type="file" />
    <input type="submit" value="上传"/>
</form>


