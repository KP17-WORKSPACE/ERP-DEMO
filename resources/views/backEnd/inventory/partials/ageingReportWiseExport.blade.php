@php
    $exportSlug = preg_replace('/[^a-z0-9]+/i', '_', strtolower(trim($groupLabel ?? 'wise')));
@endphp
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script>
$(document).ready(function () {
    $('#exportAgeingWiseReport').on('click', function () {
        var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
        var asOfDate = $('#to_date').length ? $('#to_date').val().trim() : '';
        var groupLabel = @json($groupLabel ?? 'Group');
        var reportTitle = 'Ageing Report - ' + groupLabel;
        var $table = $('.ageingWiseTable');
        var headerLabels = [];

        $table.find('thead tr th').each(function () {
            headerLabels.push($(this).text().trim());
        });

        function formatDMY(value) {
            if (!value) return value;
            var normalized = value.trim().replace(/\s+/g, '');
            var parts = normalized.split(/[\/\-\.]/);
            if (parts.length === 3) {
                if (parts[0].length === 4) {
                    return parts[2] + '/' + parts[1] + '/' + parts[0];
                }
                return parts[0] + '/' + parts[1] + '/' + parts[2];
            }
            return value;
        }

        var rows = [];
        rows.push([companyName]);
        rows.push([reportTitle + ' (' + $table.find('tbody tr').length + ' Items)']);
        if (asOfDate) {
            rows.push(['As of: ' + formatDMY(asOfDate)]);
        }
        rows.push([]);
        rows.push(headerLabels);

        $table.find('tbody tr').each(function () {
            var $cells = $(this).find('td');
            if ($cells.length < headerLabels.length) {
                return;
            }
            var rowData = [];
            $cells.each(function () {
                rowData.push($(this).text().trim().replace(/\s+/g, ' '));
            });
            rows.push(rowData);
        });

        var $footCells = $table.find('tfoot tr th');
        if ($footCells.length) {
            var footRow = [];
            $footCells.each(function () {
                footRow.push($(this).text().trim().replace(/\s+/g, ' '));
            });
            rows.push(footRow);
        }

        if (rows.length <= 5) {
            alert('No data available for export');
            return;
        }

        var workbook = new ExcelJS.Workbook();
        var worksheet = workbook.addWorksheet('Ageing Report');
        worksheet.columns = headerLabels.map(function () { return { width: 22 }; });

        var hdrIdx = rows.indexOf(headerLabels);
        var wsRowNum = 0;
        for (var ri = 0; ri < hdrIdx; ri++) {
            if (!(rows[ri] && rows[ri][0])) continue;
            wsRowNum++;
            var wsRow = worksheet.addRow([]);
            wsRow.height = ri === 0 ? 26 : ri === 1 ? 20 : 16;
            if (headerLabels.length > 1) worksheet.mergeCells(wsRowNum, 1, wsRowNum, headerLabels.length);
            wsRow.getCell(1).value = rows[ri][0] || '';
            if (ri === 0) wsRow.getCell(1).font = { bold: true, size: 14 };
            else if (ri === 1) wsRow.getCell(1).font = { bold: true, size: 12 };
            wsRow.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
        }

        wsRowNum++;
        worksheet.addRow([]);
        wsRowNum++;
        var wsHdrRow = worksheet.addRow(headerLabels);
        wsHdrRow.height = 20;
        wsHdrRow.eachCell({ includeEmpty: true }, function (cell) {
            cell.font = { bold: true, color: { argb: 'FFFFFFFF' }, size: 11 };
            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2D5496' } };
            cell.alignment = { horizontal: 'center', vertical: 'middle' };
            cell.border = {
                top: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                left: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                bottom: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                right: { style: 'thin', color: { argb: 'FFB8C4D8' } }
            };
        });

        for (var di = hdrIdx + 1; di < rows.length; di++) {
            if (!(rows[di] && rows[di].length)) continue;
            var wsDataRow = worksheet.addRow(rows[di]);
            wsDataRow.eachCell({ includeEmpty: true }, function (cell) {
                cell.border = {
                    top: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                    left: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                    bottom: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                    right: { style: 'thin', color: { argb: 'FFCCCCCC' } }
                };
            });
        }

        workbook.xlsx.writeBuffer().then(function (buffer) {
            var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            function pad(n) { return n < 10 ? '0' + n : n; }
            var d = new Date();
            var slug = @json($exportSlug);
            var filename = 'ageing_report_' + slug + '_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
            saveAs(blob, filename);
        });
    });
});
</script>
