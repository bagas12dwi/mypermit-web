<!doctype html>
<html lang="en">

<head>
    <title>{{ $title }}</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
</head>

<main>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>
                    Sisa Hari Permit {{ $dayDifference }} dari 7 hari
                </td>
                <td style="text-align: right">
                    {{ $now }}
                </td>
            </tr>
        </tbody>
    </table>
    <table style="border: 1px solid #000; width: 100%">
        <thead style="border: 1px solid #000; background-color: teal; color: #fff">
            <tr>
                <td width=30%>{{ $permit->work_category }}</td>
                <td colspan="2">{{ $permit->permitt_number }}</td>
                <td>{{ $permit->status }}</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Pemohon Permit</td>
                <td colspan="3">{{ $permit->user->name }}</td>
            </tr>
            <tr>
                <td>Nama Proyek</td>
                <td colspan="3">{{ $permit->project_name }}</td>
            </tr>
            <tr>
                <td>Tanggal dan waktu</td>
                <td colspan="3">{{ $permit->date . ' ' . $permit->time }}</td>
            </tr>
            @if ($permit->work_category == 'Lifting and Rigging')
                <tr>
                    <td>Alat yang digunakan</td>
                    <td colspan="3">{{ $permit->tools_used }}</td>
                </tr>
                <tr>
                    <td>Jarak Pengangkatan</td>
                    <td colspan="3">{{ $permit->lifting_distance }}</td>
                </tr>
            @else
                <tr>
                    <td>Tempat</td>
                    <td colspan="3">{{ $permit->location }}</td>
                </tr>
                <tr>
                    <td>Jumlah Pekerja</td>
                    <td colspan="3">{{ $permit->workers }}</td>
                </tr>
            @endif
            @if ($permit->gas_measurements == 1)
                <tr>
                    <td colspan="4" style="border: 1px solid #000; color: #fff; background-color: teal">Pengukuran
                        Kadar
                        Gas</td>
                </tr>
                <tr>
                    <td>Oksigen</td>
                    <td colspan="3">{{ $permit->oksigen }}</td>
                </tr>
                <tr>
                    <td>Karbon Dioksida</td>
                    <td colspan="3">{{ $permit->karbon_dioksida }}</td>
                </tr>
                <tr>
                    <td>Hidrogen Sulfida</td>
                    <td colspan="3">{{ $permit->hidrogen_sulfida }}</td>
                </tr>
                <tr>
                    <td>LEL</td>
                    <td colspan="3">{{ $permit->lel }}</td>
                </tr>
                <tr>
                    <td>Aman Hotwork</td>
                    <td colspan="3">
                        <input type="checkbox" {{ $permit->aman_hotwork == 0 ? '' : 'checked' }} />
                    </td>
                </tr>
                <tr>
                    <td>Aman Masuk</td>
                    <td colspan="3">
                        <input type="checkbox" {{ $permit->aman_masuk == 0 ? '' : 'checked' }} />
                    </td>
                </tr>
            @endif
            <tr>
                <td colspan="4" style="border: 1px solid #000; color: #fff; background-color: teal">Persiapan
                    Pekerjaan</td>
            </tr>
            @foreach ($permit->workPreparation as $item)
                <tr>
                    <td colspan="3">
                        {{ $item->pertanyaan }}
                    </td>
                    <td>
                        <input type="checkbox" {{ $item->value == 0 ? '' : 'checked' }} />
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" style="border: 1px solid #000; color: #fff; background-color: teal">Identifikasi
                    Bahaya</td>
            </tr>
            @foreach ($permit->hazard as $item)
                <tr>

                    <td colspan="3">
                        {{ $item->pertanyaan }}
                    </td>
                    <td>
                        <input type="checkbox" {{ $item->value == 0 ? '' : 'checked' }} />
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" style="border: 1px solid #000; color: #fff; background-color: teal">Alat Pelindung
                    Diri yang digunakan</td>
            </tr>
            @foreach ($permit->control as $item)
                <tr>

                    <td colspan="3">
                        {{ $item->pertanyaan }}
                    </td>
                    <td>
                        <input type="checkbox" {{ $item->value == 0 ? '' : 'checked' }} />
                    </td>
                </tr>
            @endforeach
            <tr style="border: 1px solid #000;">
                <td colspan="4" style=" text-align:center;">Tanda Tangan</td>
            </tr>
            <tr style="border: 1px solid #000; text-align:center;">
                <td style="border-right: 1px solid #000;">Pelaksana Kerja</td>
                <td style="border-right: 1px solid #000;">Supervisi</td>
                <td style="border-right: 1px solid #000;">Manager</td>
                <td>HSE</td>
            </tr>
            <tr>
                <td height=10% style="border-right: 1px solid #000;"></td>
                <td height=10% style="border-right: 1px solid #000;"></td>
                <td height=10% style="border-right: 1px solid #000;"></td>
                <td height=10%></td>
            </tr>
            <tr style="border: 1px solid #000; text-align:center;">
                <td style="border-right: 1px solid #000;"></td>
                <td style="border-right: 1px solid #000;">{{ $permit->user->name }}</td>
                <td style="border-right: 1px solid #000;">{{ $permit->manager_name }}</td>
                <td>{{ $permit->hse_name }}</td>
            </tr>
            <tr>
                <td colspan="4" style="border: 1px solid #000; color: #fff; background-color: teal">Proses setelah
                    selesai pekerjaan (Housekeeping) (Spv/Pelaksana Pekerja)</td>
            </tr>
            @foreach ($housekeeping as $item)
                <tr>
                    <td colspan="3">
                        {{ $item->pertanyaan }}
                    </td>
                    <td>
                        <input type="checkbox" {{ $item->value == 0 ? '' : 'checked' }} />
                    </td>
                </tr>
            @endforeach
            @php
                use Carbon\Carbon;

            @endphp
            @if ($permit->status_permit == 'Close')
                <tr>
                    <td colspan="4" style="border: 1px solid #000; color: #fff; background-color: teal">Pemberitahuan
                        Penyelesaian Pekerjaan Supervisi(DPL)</td>
                </tr>
                <tr>
                    <td style="display: flex; align-items: center">
                        <input type="checkbox" {{ $permit->work_done == 0 ? '' : 'checked' }} />
                        Pekerjaan Selesai
                    </td>
                    <td colspan="2">
                        Nama : {{ $permit->user->name }}
                    </td>
                    <td>
                        @php
                            $date = Carbon::parse($permit->updated_at);
                            $date->locale('id');
                            $formattedDate = $date->translatedFormat('d F Y');
                        @endphp
                        Tanggal : {{ $formattedDate }}
                    </td>
                </tr>
                <tr>
                    <td style="display: flex; align-items: center">
                        <input type="checkbox" {{ $permit->need_permit == 0 ? '' : 'checked' }} />
                        Pekerjaan Membutuhkan Permit Baru
                    </td>
                    <td colspan="2">
                        Tanda Tangan :
                    </td>
                    <td>
                        @php

                            $date = Carbon::parse($permit->updated_at);
                            $date->locale('id');
                            $formattedDate = $date->translatedFormat('H:i');
                        @endphp
                        Jam : {{ $formattedDate }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</main>
<!-- Bootstrap JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
    integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
</script>
</body>

</html>
