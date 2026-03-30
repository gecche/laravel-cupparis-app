<style>
    body {
        font-family: Courier, "Courier New", monospace;
        color: #333;
    }

    table {
        border-collapse: collapse;
    }

    td.smalltext {
        font-size: 0.6em;
    }

    span.field {
        font-size: 0.5em;
        text-transform: uppercase;
        padding-left: 5px;
    }


    /*td {*/
    /*border: 1px solid #ccc;*/
    /*}*/


    td.data {
        font-size: 0.6em;
        padding: 5px 0 5px 10px;
    }

    .strong {
        font-weight: bold;
    }

    .normal {
        font-weight: normal;
    }

    tr {
        page-break-inside: avoid;
    }

    tr.borders td {
        border-bottom: 1px solid #cccccc;
        border-top: 1px solid #cccccc;
    }

</style>

<?php
$fields = $foormAction->getFields();
?>

<table width="100%">
    <thead>
    <tr>

        <td colspan="2">
            <table width="100%" style="line-height: 5;">
                <tr>
                    {{--                <td width="100%" height="20"><img src="{{public_path().'/siges/images/logo-siges.png'}}" width="100%" /></td>--}}
                    <td width="100%" height="20" align="center">
                        {{$foormAction->getPdfTitle('record')}}
                    </td>
                </tr>
            </table>

        </td>
    </tr>
    </thead>

    <tbody>

    @foreach($foormAction->getBuilder()->get() as $item)

        @foreach($fields as $fieldKey)

            <tr>
                <td class="data">
                    <i>{{$foormAction->getPdfFieldLabel($fieldKey)}}</i>
                </td>


                <td class="data {{$foormAction->getFieldStyle($fieldKey)}}">
                    {{$foormAction->getPdfField($fieldKey,$item)}}
                </td>

            </tr>
        @endforeach

    @endforeach

    </tbody>
</table>
