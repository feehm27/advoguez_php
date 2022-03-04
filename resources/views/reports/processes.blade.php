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
        width: 100px;
        height: 100px;
        float: left;
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
    .big-size {
        font-size: 100%;
    }
</style>
<body>
    <div class="header">
        <img src={{$logo}} alt="logo" >
        <h3>Sistema Advoguez - Relatório de {{$type_report}}</h3>
        <span><b>Nome do relatório:</b> {{$title}}</span>
        <br>
        <span><b>Data de geração:</b> {{$date}}</span>
        <hr>
    </div>
    @if(empty($rows))
        <p></p>
            <h2  class="title">Não foram encontrados registros para os filtros selecionados.</span>
        <p></p>
    @else
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
                    @php
                        unset($row['id']);
                    @endphp
                    <tr>
                        @foreach($row as $value)
                            <td>{{$value}}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>