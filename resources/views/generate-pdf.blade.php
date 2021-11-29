<!DOCTYPE html>
<html>
<style>
    .title {
        position: relative;
        left: 370px;
        top: 2px
    }
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }
    td, th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }
    tr:nth-child(even) {
        background-color: #dddddd;
    }
    .header img {
        float: left;
        width: 100px;
        height: 100px;
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
</style>
<body>
    <div class="header">
        <img src={{$logo}} alt="logo"/>
        <h3>Sistema Advoguez</h3>
        <span>Data de geração: {{$date}} </span>
        <hr>
    </div>
   
    <h2 class="title">{{$title}}</h2>
    <table class="table table-striped">
        <tr>
            @foreach($headers as $header)
                <th>{{$header}}</th>
            @endforeach
        </tr>
        <tbody>
            <tr>
                @foreach($rows as $row)
                    @foreach($row as $value)
                        <td>{{$value}}</td>
                    @endforeach
                @endforeach
            </tr>
        </tbody>
    </table>
</body>
</html>