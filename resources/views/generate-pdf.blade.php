<!DOCTYPE html>
<html>
<style>
    .title {
        margin: auto;
        width: 50%;
        top: 8px;
        text-align: center;
    }
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }
    td, th {
        border: 1px solid #000000;
        text-align: left;
        padding: 8px;
    }
    tr:nth-child(even) {
        background-color: #E0E0E0;
        border: 1px solid #000000;
        padding: 8px;
    }
    .header img {
        float: left;
        width: 150px;
        height: 150px;
    }
    .header h3 {
        position: relative;
        top: 1px;
        left: 10px;
    }
    .header span {
        position: relative;
        left: 10px;
    }
    .header hr {
        position: relative;
        top: 20px;
    }
    .size {
        font-size: 60%;
    }
</style>
<body>
    <div class="header">
        <img src={{$logo}} alt="logo"/>
        <h3>Sistema Advoguez</h3>
        <span>Data de geração: {{$date}} </span>
        <hr>
    </div>
    <p></p>
        <h2 class="title">{{$title}}</h2>
    <p></p>
    <table class="table table-striped">
        <tr class="size">
            @foreach($headers as $header)
                <th>{{$header}}</th>
            @endforeach
        </tr>
        <tbody class="size">
            @foreach($rows as $row)
                <tr>
                    @foreach($row as $value)
                        <td>{{$value}}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>