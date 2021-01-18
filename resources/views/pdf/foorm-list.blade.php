<style>
    body {
        font-family:  Courier, "Courier New", monospace;
        color: #333;
    }

    table {
        border-collapse:collapse;
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
        border-bottom:1px solid #cccccc;
        border-top:1px solid #cccccc;
    }

</style>


{{--*/ $fields = $foormAction->getFields() /*--}}
{{--*/ $pdfSettings = $foormAction->getPdfSettings() /*--}}

<table width="100%">
    <thead>
        <tr>

            <td colspan="{{count($fields)}}">
                <table width="100%" style="line-height: 5;">
                <tr>
                {{--<td width="100%" height="20"><img src="{{public_path().'/siges/images/logo-siges.png'}}" width="100%" /></td>--}}
                <td width="100%" height="20" align="center">
                    {{Arr::get($pdfSettings,'documentTitle',"Titolo")}}</td>
                </tr>
                </table>

            </td>
        </tr>
        <tr class="borders">

                @foreach($fields as $field)

                    <td width="{{$foormAction->getFieldWidth($field)}}%" class="data">
                        <i>{{$foormAction->getPdfFieldLabel($field)}}</i>
                    </td>

                @endforeach

        </tr>
    </thead>

    <tbody>

    @foreach($foormAction->getBuilder()->get() as $item)
        <tr>

            {{--*/ $itemArray = $item->toArray() /*--}}
            {{--*/ $itemArrayDotted = arr::dot($itemArray) /*--}}

            @foreach($fields as $fieldKey)

                <td width="{{$foormAction->getFieldWidth($field)}}%" class="data">
                <td width="{{$foormAction->getLabelWidth($field)}}%" class="data {{$foormAction->getFieldStyle($field)}}">
                    {{$foormAction->getPdfField($fieldKey,$itemArrayDotted)}}
                </td>

            @endforeach

        </tr>
    @endforeach

    </tbody>
</table>
