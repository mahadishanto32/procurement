<div class="row pt-2">
    @if(!isset($searchHide) || !$searchHide)
    <div class="col-md-3 pt-1 pl-0">
        <button class="btn btn-sm btn-block btn-success" type="submit"><i class="la la-search"></i>&nbsp;Search</button>
    </div>
    @endif

    @if(!isset($clearHide) || !$clearHide)
    <div class="col-md-3 pt-1 pl-0">
        <a class="btn btn-sm btn-block btn-danger" href="{{ $url }}"><i class="la la-times"></i>&nbsp;Clear</a>
    </div>
    @endif

    {{-- <div class="col-md-3 pt-1 pl-0">
        <a class="btn btn-sm btn-block btn-info" href="{{ $url.(isset(explode('?', $url)[1]) ? '&pdf' : '?pdf') }}"><i class="lar la-file-pdf"></i>&nbsp;PDF</a>
    </div> --}}
    <div class="col-md-3 pt-1 pl-0">
        <button class="btn btn-sm btn-block btn-primary download-excel" type="button" data-name="{{ $title }}"><i class="lar la-file-excel"></i>&nbsp;Excel</button>
    </div>
</div>

@section('page-script')
    <script>
        const exportReportToExcel = (filename = '') => {
            var downloadLink;
            var dataType = 'application/vnd.ms-excel';
            let tableSelect = document.querySelector(".export-table");
            var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

            // Specify file name
            filename = filename?filename+'.xls':'Report.xls';

            // Create download link element
            downloadLink = document.createElement("a");

            document.body.appendChild(downloadLink);

            if(navigator.msSaveOrOpenBlob){
                var blob = new Blob(['\ufeff', tableHTML], {
                    type: dataType
                });
                navigator.msSaveOrOpenBlob( blob, filename);
            }else{
                // Create a link to the file
                downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

                // Setting the file name
                downloadLink.download = filename;

                //triggering the function
                downloadLink.click();
            }
        }
        document.querySelector(".download-excel").onclick = () => {
            exportReportToExcel(document.querySelector(".download-excel").getAttribute("data-name"))
        };
    </script>
@endsection