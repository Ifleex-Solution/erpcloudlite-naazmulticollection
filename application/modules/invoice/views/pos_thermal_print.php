<div id="tp-root"><style>
/* ── Thermal Print Receipt — 80 mm ── */
@media print {
    @page { size: 80mm auto; margin: 3mm 4mm; }
    body, html { margin: 0; padding: 0; }
    .tp-no-print { display: none !important; }
    .tp-receipt { width: 100%; max-width: none; box-shadow: none; border: none; margin: 0; padding: 0; }
    /* Force all text black, force images to render */
    .tp-receipt, .tp-receipt * {
        color: #000 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .tp-items-thead tr th {
        background: #000 !important;
        color: #fff !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .tp-logo {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        background: #fff !important;
    }
}
.tp-receipt {
    width: 302px;
    max-width: 302px;
    margin: 0 auto;
    font-family: 'Courier New', Courier, monospace;
    font-size: 12px;
    font-weight: 700;
    color: #000;
    box-sizing: border-box;
    padding: 6px 8px;
    background: #fff;
    letter-spacing: 0.3px;
}
.tp-receipt * { box-sizing: border-box; color: #000; font-family: 'Courier New', Courier, monospace; font-weight: 700; }

/* ── Header ── */
.tp-header { text-align: center; padding-bottom: 6px; }
.tp-logo { max-width: 110px; max-height: 50px; display: block; margin: 0 auto 4px; background: #fff; }
.tp-company-name { font-size: 16px; font-weight: 900; margin: 0 0 2px; color: #000; letter-spacing: 1px; line-height: 1.2; -webkit-text-stroke: 0.5px #000; }
.tp-company-sub { font-size: 11px; font-weight: 700; color: #000; margin: 1px 0; line-height: 1.4; }
.tp-company-sub a { color: #000; text-decoration: none; }

/* ── Divider ── */
.tp-divider { border: none; border-top: 1.5px solid #000; margin: 5px 0; }
.tp-divider-dashed { border: none; border-top: 1px dashed #000; margin: 5px 0; }

/* ── Info table ── */
.tp-info-table { width: 100%; border-collapse: collapse; margin-bottom: 3px; }
.tp-info-table td { padding: 1px 2px; vertical-align: top; font-size: 12px; line-height: 1.4; color: #000; }
.tp-info-label { font-weight: 900; white-space: nowrap; padding-right: 2px; width: 88px; color: #000; text-transform: uppercase; }
.tp-info-colon { padding-right: 4px; white-space: nowrap; color: #000; }
.tp-info-value { word-break: break-word; color: #000; font-weight: 700; }

/* ── Products table ── */
.tp-items-table { width: 100%; border-collapse: collapse; }
.tp-items-thead tr th {
    font-size: 12px; font-weight: 900;
    padding: 4px 4px;
    background: #000;
    color: #fff !important;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.tp-th-product { text-align: left; }
.tp-th-price { text-align: right; padding-right: 10px !important; }
.tp-th-qty   { text-align: center; padding-left: 10px !important; }
.tp-th-total { text-align: right; }

.tp-item-name td { font-size: 12px; font-weight: 900; padding: 5px 2px 1px; color: #000; text-transform: uppercase; }
.tp-item-vals td { font-size: 12px; font-weight: 700; padding: 1px 2px 5px; color: #000; }
.tp-item-price { text-align: right; padding-right: 10px !important; }
.tp-item-qty   { text-align: center; padding-left: 10px !important; }
.tp-item-total { text-align: right; }

/* ── Totals ── */
.tp-totals-table { width: 100%; border-collapse: collapse; }
.tp-totals-table tr td { padding: 2px 2px; font-size: 12px; color: #000; font-weight: 700; }
.tp-totals-label { text-align: right; padding-right: 8px; color: #000; text-transform: uppercase; }
.tp-totals-value { text-align: right; white-space: nowrap; color: #000; }
.tp-grand-total td { font-size: 16px !important; font-weight: 900 !important; color: #000; padding: 3px 2px; text-transform: uppercase; }

/* ── Footer ── */
.tp-thankyou { text-align: center; font-weight: 900; margin: 6px 0 4px; font-size: 12px; color: #000; letter-spacing: 0.5px; }
.tp-terms-title { font-weight: 900; font-size: 13px; margin: 10px 0 3px; color: #000; text-transform: uppercase; }
.tp-terms-list { margin: 0; padding-left: 14px; font-size: 11px; font-weight: 700; line-height: 1.5; color: #000; }
.tp-poweredby { text-align: center; font-size: 10px; font-weight: 700; margin: 4px 0 2px; color: #000; }
</style>

<div class="tp-receipt">

    <!-- ── COMPANY HEADER ── -->
    <div class="tp-header">
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="210" height="79" viewBox="0 0 210 79" style="display:block; margin: 0 auto 14px;">
  <image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAANIAAABPCAYAAACeYZvfAAAQAElEQVR4Aex9CYBdRZX2V1X33vdeb+l0kk6ns7MkMRB2cGERBwFlEXTQEWRTccMd/3GccZzRUWd0HEUdN9ABZXFjUBwGHFCURUSWsJMASciedLo73Z3e3nt3qfq/c1+/TgJJv9dZSeibOrf2U6dOnVN1qur2i3Y7+diYCCzBJXwVnbMF55z4sUui2DnJK0PCQFohYXrsbBK5yLlhCFlYQNLELxJn6GLmJ1vBlmkuSdzW4Bgn2BIwlxisi2wyBI5xR8wliF3Jl3ICMWkog8QTF7nNJRgUZ/kSkALEy444gSQO6ccE54jGSf/zzrGtIt8FN7Cpl9isK/ANwAkkSeKstSmw0JjbRzmgOZg75ZwBrLKgoBIUpSMgPgEDbQxiikvClBSUQqIMLDQsfac9eFEEL47hWQvfuhTK4cACfmLgJRpeTIgI9IfTmB5qDYEifYGCBooCCggJ2lkY5+ABQyBxC53EUFHIPEA7AqxQBcMeGCQwNmaZMK1VytEsgRSgwFIJnEoQsVZIOq3SgPFh2a8wZHrEWux/FiE0aYbNQNXVw2e5rPKJd8ztTxzg6O9cd6hCcEShtQ9DcFQWyhUFCikoHQIqJjBVIX3iBBBI6MOjUBkPUCRlCKyj0A4BJRoCVsdwHit4xGMsnE6IP4aXRAhcggwsMlSaLEF8gcAmYEE4q4dB4gDb0h6UF0AeVkFCwY+pAOI7KihE2HUgJVNQbAOOfXEFVilCqRBx3AdSj4BK7NjFOCSN7FSQUfA5C1iwrAvw+S9fCWUUagisPOwWLltGPGo4PhbYdzlAido54hXkH/gmWMotBSrhFO0oUJrCbZyCrAiGgmgQc8a3CKgIAr4BipTAkCtAxFUtVg4CCfOtcZDVLqJCCVjtwSrDkjoFCTumgcpriTVONMJYo0gIGbaiPJLOHlq2UwZmEQ+4eoJlbRqWdpTvQQc+QN8azZWG+ZYdigA2COUMVy4qlssCLgPYHIxuRL6vkwVCGNLss57iKpRQ9d793sthdA5KK3zh859mmZe6Iw6YOpyolBoOjwX2PQ7onSVZU8Ao/TRfkCoT5Qg+5dGYBI5mDRJGLEF8KgUQI4p60dfXhvb2ZciQgkA5+FQ0jxIr4MPSDEtgWJY101nfc6BCJqwewkVcEbgSGRexRhFWhfBMjMAPkSEEXghtilCaQCpA0M5REZISwMJn+YxvoWyYAhKWZdgwT8CnYmeogPDzbLgIyMrKVRHaph1NFLGSdlUT4Oklz+OSy94H7RkYpeARfnzNDzHSk49ivPDsUyMVGcvbOQ7s0dp6Z1tTVB/NWReGmKgMLub6wv0Ht+iIqGSOCpVQ3GPmJ0qjiAC3//4JnH3uezBt2nwo5ZdAByW/HC/7xK2GwYPnZ+AHWRiaZYp1xtdPxBELjsYV/+8f8Ns778FgQXFfFrDFDBxXDqFPwFG4lTJQSpHQzU4pj2ketPGhtMcMYUkJEigaZ34KA87DvY88gfMvuQxKKXikSSDnj8OCuQtw3TXXsG71TnsKB807KsWl1NY0VY9lrOTLhQMiMTtFi5PaIgecwR1XFuUHAIXcqQwSKsNPb7sDnufBF8GjwGQJ555xMu75wx1cmUKpvVMwMNCPZ55+Gld+/Ws44/TTUJsLhtvSbNOwvTIopSi4ZgioOIqgGRdIlYzhtIyUU/CURo5lcsyrY5nXH3sMfn7dtTtFr1QucHVUDMQ0AemNuf2AA3pn+2CpPJZKBJpEYrlxe4RT33g6MhTIGsKFZ5+5s03sV/VXtK+D7d8In3s4MVf3q869gjtTWZG4JYCN0D+4kapiuWuhyqTLkEU+34He9jXQ1KA/PbQIhrN3xij88a47X8Es3X7X//qd78Lk8c3I1U3A2GK0fT7tizkVFWnx0iWw2kddzQQ4HhF7oEHC4+yYdz9+dhKyzdOhaEKd+OrD98X+71Gar//pDQh4ICGNWjkRlMArCvbfzlZUpAPmHMwzL6BtbReM8RAW+7kyRShy9XnVvPkQ823/Zc+u61n7wCDNXVmIZIkHD0IUQIexZ7/gQEVF6u3flI53S2sTO2wRZBrQWzSo8zSWPr+YaWOuEgceX7IcTTU5yBE7eLIp5ZUa0yLhw/4CFRVpYm4c90C8R1F5JBSCpWs6MD7LC8n9hQO7uR9XXXs9Dj1oFkxqEnNzSQUKeT0wtkfazYzfw+grKpLMmxs7OmjO+fjl//4OB09v2cMk7rvN/eDGn+OySy9EcaAPsDGghd0elDKpHjlJ23e7N0b5FhzQW4S3HdQxxk2chvseeQYXnDt2lL1tJr00Nc+7ossuOBftbW2oqamlL58SeUhYVPNwhh4Urw7E36sw1vgu4UBFRbJxAbECTj72iF3S4CsByYrOjXCFTRjs3YiWFlnBNZqntCKR74rIABp4iHnqiTFFIjf2DydfjtHq4Dwpo0uQM6WEhpyl2eFsCO3VITc0g+4fXd69vUi4Ek0bVwPfy6G2obXUGCciKA3DOzbDFE/Ak3fA0JjbHzhQWpFEUSgAPJOl7W6xYUMboDXHPiCo/aGfu70Pl33k/0E+/Xny8cdhvABW6fTaYLc3PNbAy4ID2joLTaWhwQ7ruCHmyVzL5BZEXKSWrVz/siDy5U5Ef2Lx/f/8WvqV+hFHHIYOHs54vHNj8sud9DH6dhEHtHyYmeLiwiMK5ahIEo9o4x00a8g0kYQxeAkH3vfhj3MR5+V0dyc8cEIirFixCpMmTebKDgT6JVWqShgrtO9xgENtEcnyI7QrBcXVaUP7Rtx+xx8kZQy2wYHWaTMQW4erv/UN2CRB04QJVCHNEzmNGbNmpTXiiHdvssKnsbHX/s6BVJG0bygISIVCOjypeRL+5uxTJTgGW3Dgovdehpgr9trVy2CoQAD3QSaTKpBjuSR2NI8ZoJM/HeExHUNj7pXAAUpCAsWeiiA4Cgacwk+u/zlTxpxwoKV1Gn7zu3upQA7X/fA70Eke4L6SR3Dpn6NbrkxSTpOBgZzKWaAwyDLKAN7YFyDCm1cCaCiFKE5AOYAyHHwKwGUXn/9K6Pt2+/jxK/4OceQgvzux8KE78ZY3HgPDNds5KoaqhbUB5GDGN0X4SsGQeTIZgXxUGsjmcggTCx7dYOx5ZXCAwx7wzsNALpQ0aNfLqvTK6HvayymtU/HYU8+nK07ElSZ2RXzt65/nETaXFqcxrnUuYHNAoqFiB6tjWBOjr60bITKcgGQSGgIPw3GfS5ShGSh/A0xMkkEcbDKieqVmoaRaqifTqHQSsFRExkAtTZWwJ4oA7l9ZA3kakNLKoBQoAuLH8leUjJcwsRrDUkbAyXWGrJbsU2piChKiI4HEZPECzVOwB9IurMUzq19ARz6fRqWMEGD5SoZw0kOazklDwtKm5JEU3P/kY2DRND+RAPeGXJNh2d5w/yVdLqFZX+oKOQIkBtJoTOSFIZCwpKV5nNCkwOLVS9gsa7A+32zPUVpJgSAT/hF/b9iLJeuXoyAVWU66lxLhImKOWCJkTjwEpd6FTHW8M4XQxkaFXuGt+KwEsC8ySik/2RyLsN2UcyAi0lRyOkkiOB7RadaKofBPX/pqKWcXv3M1NXjTGWfi7e88Hxdecinedt7bcfiRR+KoY4/FoYcfjnnz5+/iFkvoXn38a/DBT1yOPz58P4oUrpiQEByVIio4rFm7BAvmT0tXFWoTORBwKgmQMk4DOt+fMqzIxboYKCKVz3w8jGuZlPJRcQUfCQIZaAJlCvK7FbHv0TeIrEJnZw+5DhRoEsbSFv11G9ahKxwgDRaN8kmJNli5agVyzsAUFArtHeARIW6+7dfw5FiQuEuTIFgHUPxnCEopRliefpGWRkglj1VMwQCM1Zg1/UAKRCByAZkk5k2fhUl+FmrAoieOsLqngyKmuReU8iotFylAgE2mE6+hYPWvbMNRhx2JZWtWQR4ROjYPn4JJ0hGwjKKg98UhYk+DSGGHJoCulSsRGoDkwCM/somCgIQlTfIS4RDzDpg+G0nMlqn0JANgv4iNc4AjDo11G9rRgDoc3DQTAe2Hp1YsQxKQXucD8KHoe0wwsQfDwTRkuCe/meg8KGGoACfLgJNXTVxEEIZAGGFlVycSpinlIL9uFVKxMuxTQEUV+og8ddpw8AxnTyjmkoQvfu4zaUa1r49+8pMIKZgC3f29lMUkhTwZFzFdQPJ6Bgbwm1tvxXXXX49rf3wNfnnTL7Hw0Uex8MG/4KnHH8XiZ56C4wxaBktBTxIS7wbRl99IU6kPCcNlCJM+PPTon1DgDC9mlAi+QMLZPuYgWjJc/D/98c/4/pXfxcnHvA6ajHEE6a5IHe9Nsam7Hf193exuDI8nlgn5l1CAHRVkfdtq5GoygLJY39nO4QFMAcgQf0h+cZw4zNGIIDOaVhEsoXuwH+19felEqXkJPnFCEx5dsghZC8hP9oWcGZt5h9fk56DZL+TABoHaugbWZ9gHGponMmBxwZlno2v12jQM0geufnJ1oYZ8CVsOepZ8yNDvblsF5XEW1jHaujtSRciwr20DfZwQLPtAVOnbIef7mDpxEoKiZbsxU2OWT+CTtwI6iWFdxHoRJpBeEaops2YIAijqasKQKhK5lgCgtKapzAwwQTHOCSCRvk6ZyhQHoZnqhFTLIOVsmqbZcuKzDldlj4KuZASIy4GP4KF8xZ5CwCqtTa3o6N+EMKOhSfeCqbNZ2sI3FgkHtU/ydBGbbB8eeeEx3HzXLbj+1p9hyaplWNe7AesH2rCyaxXWD25ESJwhaYwzBs0TWqD8gBNfQsoiBIZtM9TT0U7qJFwCvei55wFFqmgCFMqmBbb9/M3Fl6LAco5MLA70wMUFfPvrX6a+5wlFNNYEUBw0kD2e8VMkHuM+1/iAfkDhyxoHj4NtmGYH+yCcTyTM/MFCLxwFTtIUzSelIx7N51CTbYLRdQAlq6OjQMbk0vjRRx4P33MMJ7DsnKWIRo5GBesKHmUSKocqjZEDZ3AHTa73DGxAR+86dA+2Y1NUh5pxrcTtEViGniEjZX80hQPdJzxh2vTxk0A5xMq2JXjPpz+MjPbgkW9aBRgJlOdD6QBZlmuurcfNv/gJejesgokKAAXy2Nnz2C5p1EDgAmzkDG+NQcjBC6ExQH63jG+CKH9HOIgVG6g87N9zq5Zi+vy5UCyrOPUr0qNfBIZjoIwHpX20TJlJejPwlY8pE5vxszt/Dc5M6O3qgfE1Ons2wnpMqjVpW5p6MKAKMMShCSkez4NKwYfxAniEPy9dCFOM4LEX1DPyO2AIzPc4JkCRMzkUMN7kUv6FLMihQMAOLVzyOOnRELqVMcTtl4BhSfOVxq/uvA0gfdQHaGMAqSwtkC8eWw04qdFD6AN1TePR3d0DIoflOK/tWg+lDOSXpxoaGjlmWTRmGnDs/KNx3pveiovfegHmzToIU8e3oLVxCmY1z8QBU2ajr72dS4qHle2dGFzRDg0Fp31kYJB2ymjUNzfD40SPoUfPmTsfwDA0pgAAEABJREFUUJoFHDJDfwaNLZ6b//tOCrOD42rx8x//iMhEaD24zDgk6dFvjqtFjjNUhrUyHIQAupjACwlc0hN2JCIp4lvlISGIb0mYqaknp32aD6zHDXxN0AAlv3+XjqgHZX1kjIUqFlPQnF0nTxhH5lueniXQZKJmWR17DAsEyLhaGJuBJj4dB4AOU7A2n5rBDjl0bLSYOmkWmuomY/bkiQiUgtIKHT3d9C3ZxTYdu2MVar0a9g9MU7jv6Ucwa/YcXHvl1czcMfex930UU1pmQgU5KM78f1h4LzoL3Ug4mQjGyTNaETFgye8g0VRAjk0CxH0FTMrU4CCuACefdQYOmfkqDPYPsOSOuQve/Da89pyTMW/6dCIHWmobUxm997FHKDYWtG1Ql6mviPz4BccAWYe4tw8i5yCthuPdF+aJz0IphaJg4XjDgmnAsmeeRmd/D4459FjJGRHOP/0sWG05acuAlIpaQaQZpws9TfkkxyhrPrPrA4MNm9bBqAymTZjGlNG5Qr6AiZOnQymFDWuXYkJrM9KmaO6CZiAov10sw6Zh4s24teXqwDGjpGRghEDmTeTN/JOLl0NWnre97SR4hsLIFQOpjWqgWMeYiEyJoWwRAYUdnCHWd3Sw06CSERM7ZJUmTgdPIfU18csyG3OpTWyCiMQVMwr9KkFRO3QXBtAfU+A1aPdaREyLtUacCUrAcKQV0jRjIEzsTQpYsbEthYcXPYnb7v4dfnTDdfjwJz+OGQdSYHWGTGHfqBC+n4FHBs2ZRWGl3cuubuXGc0Z79rlnuFDE0MYwT6V6GLBd6AgnHV554DHK55TXvAGTapvw7JplsFzyumn6ZRKRTQXKI0wMFDzANmRBhuMFHgrcc9tdo2xl28X/wmP95ulTAI+NcIQMD1cWzJufrhynnHHGtittI1VRaLMNuVKOAjjfIaivh2IHAipPPgrBuQ4UFgQU+LmHHIzm+mZU+9xwyy848RAxKyScVNkCLMdR+BHECu20bAxl0OMl+EmnnYKW8Zt/wZZVdtgdf9TxUJTP7mIfDOVbtihFklHjZ6HkECRgZAi7FnNLotJ5ITDPk5v29W1YMHcW6YwxSKGPlUOiqYO8uAU74JyCJdMdmX/bHXdDVjVDwWud3AzD/GEwisUNtPZSXymDDO9WBALjIesHnHEV6rkBz1JYm+rqUJ+t4YqjkGHZQOk0LL6AHDWXfQlLmXGc2We3TIHAcTy0OOvU0/C+Sy7B9779baxetQrVPldf90tw2sO8OfNgTAAoRgkQfWLXOwZ6sDufQ2e9Cv/yrX/DQFc7oDmRkOfpmCgLy4Y35QcRUpn/6Sv/xtiucx3r2tAXDgAeuGHvSFc96AR/+O3olPVL3/kGKBSAc3AcS8VOaJmhSXwNxzcEECmHuDiAcy+5iLHq3QcvfD8SFrcKMJQJTY4QLdtioqcwtb4Ri5c8AxVksfDPDzNx17qmXAPW93VSmWJhE3yugspQKDg25ZZ0WBwEaQMVO03LZhWUAmLOHBGZUWPqYWhuGSh2RuFz//KvFDSDDJVDhPotZ74JS55/Dvv6c8lFb4cnjODpDscJIQ875Pf68oY9o0Dc/L+3MLB73Rc+8Y/44lXfQb6nCz0rVyM95eOA1ZCgSX4NZDa/8Xs/3uVEGDmhItZGnkRykPHIsmWMjc7980f/jkITIvFFTgDqDCCSr4GAtp0iYl8r+DUZ/Oa6mzCaJz8wiHwxz4mdtWjp8E1sFGTiRtEhob9gzu79e7kZNBMNF5Cws5c7cdBasgAXGaFFQAdePZhEYhwJpd6LINHzOLMseeYhyKb92RfWQCkfHgXtS5//rNTbr2DFes42aY84IprcoBcYBcXZJYcYCWe9b3yJM25aZve+rv7KN3HzfbfDH5fj4QM4C5p0fMAVA1zNd3XrBY5zTW4iZCUxgpyvow+aLaHRA01TUL4dinA8qADDxZgrOWfwnDUo8DTi6BOPHz1e1qjJ5CCcWNWxkqanMIOEcskOA4Vx48axxO53E6e2QE1sQC4GfLFVKSPlVjUUO8hVKaCOJzzH7+5kx0kjaMYdfMgJ3FzPx/w5M8vl9zv/7j8/gqktE7jZ39w12cfJtKKpQJtT91zoonMuwvimcRTH3dvmbXfcTJuDbSgFzpQoP3J1UQ6Pxlfch8qeLuam3BPNpxz5QU4kK0WjKXiP/umRNLxDL85xUyZzT8cJnRQThcLqtpUY6O1nePe7je0bkeWhEK/z0j2fk33SULNyJYCajMfJI0LAvcH4iU2I+O+mW+/kPkVh1YqlQ0Wr8PaxIv/4uX/GCa89GoV8ETq1dzlS7IPxPFjOdrTuIDM1kxANmT8S3hPwgSs+BrGPRMbBx3HloLdLXCPvr55f9wLO+KtzocsYuXqIcNJ6wbJVK8upo/dVDE2iNTRsitxA9kaC6CN/+wnxdhw4PJoKSkOBqxP4OBzUOot+9e4d730XejvWIyn0Y83GNfirc06rvjJLKuqIMgn1haLBQwcmpU6rAOApMsnzmQOEFKAHH38W73jL6WmB/fW1bG07vvj5f4Bif7O+QXnxWb16dWpKKR6eaLW596FcMG6ObjP0lgveRr1LELsiEleA4z2DnHw6hq+79aZt1tle4tVXXgU/tcY3l6hWl556/nG4QgTHOzAx3fI8cYp40uqGoKNzI2bwvqS9MAhDBSq3oCkeokh33XNPOWnU/nFvOAE54SD3R5R7rkYavqYpRgX74X98Fzv+EBvv3UAFjYhEEbr7O/iuzp189huxMd+HC99xMeontECbWjQ3tuCuX/5fyqfPX/llVPtoXqVkeIItdJTr8Cwugkct6+7oRaGwCeBl04lHH1bO3y/9f//PH1CQxrNvHnjABI82PCPpKjR1+nQG06mU/mYXVrEi3Xzjz8DNJnoHIix+fgVP2UBVoBDFPi46/Wy0rV2OR55duBlphdATT242g1y1WkSchx50CN8eBjmWmSKQlSmcp6xcKwD6XghkCM01dSxHp0rgOKkwhJt//t/i7RA8/KcHyQOQl4Cgpe5C9jY//5/RTSR4yUNFoqKDiqSYJyPUxItUBiu6d1/xQfz6BzdgfJLDm087DalJliJJYGluypj9/aWfwrd+8p2KuKTAG07hIsP6+S1kgpMuUzgrjedyr2nPZhTjUno/hV/d8Xt86iMf4GBrhLwbW7d2bdrTJEkoY9J3DhEFim+k0pDmVvfq6+JExKLjs7U4ZNZcBNyUBhTYIhW16PuY1DINB9bOwFU3/oilKrtjjjxpuJBSQttwtHKAU2Rag/crItEea8RJBC4U4FKHhJbIE6uGzPa0ILg6y84Q+NOdd2NnnrWbNkCWeFEgj8pMRuL8cy/YGZSsS0USoWdI+mItmctwNe57X/oKGidORlJrIKtIvrcHYXETFq9YISf9eLZzHdCYwYcuuqwadLj/7j9DPl5u9MnEoRrcGpCs9KSKhHFlGkrfL70iZ/VzTjsRcRhzFdYED62tpRXIGB/ybZ/jrCf7I3rQSgOjEOA6HhD0pJtR4WWMNf2dAFFkQI8X0FZ7aJgyERdd8F6mVHYpHZWLvbSEinhZHYu+8MRRsjlJcNJQNFdjWLSvWY1iZzcOn3mAZNL84kyqQFJVGt/Zl3xRQIxEwzWQgQcXPcbwzjpX2uATjdwfu1QlGKngNvT3IpsZB9C8lY9QORzITByHIFuPw6ay/zwdmdXaAg4PT+ICvPuKy7eN8UWpa7poViabE7kiSaSI7u61OPF1m2dASd2fIIlDBGJjFw28QMMqCx0LW4HOzi5YdtYzHhT/GQoco2T+FpxKE0Z++cweLBQgl8tgWxMaG2E9IKJyKeE0G7EGyHGVuvqnP2Dp0blqdTofFhAoB68YY2VHG6AtFE06oc9RbZqnTUUNhWlJ2+qUAJe+d+1rQP64EcScANdff8NOI5cJbiAspuMkr1gUowqszVSYPId5MKPh0SrIkKSQdOXFpud4D7o8MlGMgOMCjv01X7sS1Tw9fbzE5j6pXFbLd0yg7Th+/BQ89MB95fT9xu/u7kYURdBccUDG6YyhUKkUnOy0FTCRJ5XkNWdkpKCUAnkKVpIxSzfkcRih0lPkALXW1QO8RkiCgJtuj+0kMNqHF3nEFxO/BRi89PxLKqFL82O+RYioF1RsRqpwNZkmKJWByvo4sHkqlA6gPAOjDLIqwPW/+SlpdDi4eSYsDwE0FA9HNJyU2eKScXtNuSjcXtZw+nPPLUQRGVj29btf+fZw+rYCt9/zf9tK3iptbccG1Pq1CLjrBHHOX3DkVvnbjRggx8waArsJgRxMmsbBgPLqCQEnVQfLmSbUGtU8S5Y+B86Yw0W1Fc12AId3OHF/CBw8bz56ir2oq6uD3g5zlFK7vauKGqDJ3xc35Is0vDhxD8UvfutFOPDQeVg/2EXVLxG3mRWl+EikJB4lbqQCzDvuqBNpKjkMFvOMjezefOJfjVyAud09PXwPySlJfGHxkjS+s6/hnuiSLASiXVUgve+P9/J0drPWaHCWkrpWVLUKBPtCETnyfWbxY+jrWAOPd0JbKlL59EupEuN2f38suDUDJ0EkFNu0PQpCOZjG98LrhWeXYu70Wegf6Jfhx7Cyp8SOTJCskhdcfMHIhZirdYzZs2YxVMHxVLNCCXTIHzRyzMhNVCuql8ihEkZ+PBkLxTIapSGxDFfh7r3rbqQHN0NlqYfEQmQuZedQ6j7q9UYDaO9ZS4Mihs9jqWktr3pZ9ITsTelQZQmwjIbyor8XXd+mPl55FHk4uQUtFKhKJEl/brzq+krF8LWrv4nOtvYRy63vWQds0fz2CqeKRP5J22VR3V7ZcvqJJ1Wx508RAjI5pGSkrzKG7ftrV61h5mZmlUIpslKQufucW7ycp1A2RD1t/EnjJmKwd8ic2EaXlKI4E/ZkJ8vNudKcB8oDzyJivByeuvo67uNISSoD9LdjBjNn2MnWkresw/HtBT79oU9vL2s4vaVh0nB4pMDadVS4oQJVyvpQ6T3jcUWyHFTHsVV7psVd1Mqll32QNmqRl5/9mDerFYFIa8KdpQ1QU18Pa2J09rbRrHIp7KJmdwyNYzWCoiKRGsiMamoDJu5dd+V3/h3ZIAslykP66KpZHOCz4KABvvD1L+9UB776/a+n9aPhjUoa3eartCKBckpg+6jiue/eeyuXUqUiHr103k1fjFRwsw8+gLRsJoQstDBGsL0c9fylvVmyej1NEYdrr/4+BjoHUc/Lz4G+AguSAxzcKCmCPUQSJZjI1QlDT3lvNBTdY15CYnjynJovwuV0L8LA3l6P3vy2s/DRD3/qJXxI9yAvSX1RAumnwz9d8ZkXZYwu+un3X4FYTg05dJVqrl1TujhX5CfPbyoVT/N/8p2rUn+kl3z0wflteHxQBS2C79QzTt+qaOnUjpXTmVJKvAzhltvv5urjkNg8Dpo2DkqFgALqJjYidCFy9Vn6CWKehBkvAzmI9OnDeVBKpTUZ350AABAASURBVAA+e0OZLGQdksbB8wYNpRRkk7pszXIm7h33zMrFuP3m30AnPPIuk+BKAZtKVSm8/bdFLmHuFvcojI3ecTbp42FHaipWqC3fOiqWEaC3y9zwpYYtMSCErQr3CSefxPHcTI12CPDc8w/STuZ0XhWKPVMok8lixZInIcJ/zptfT6IBrbJsXG4FAvqM8x0YObDUCJQPj3HaqlCaATUE9MQppVIhlvBoQFDxWgheUNn+yJDKlT3dRK9gkog3HjY9qTOWg8PqmpcqkQgv8951wYWo5pE+KQq3fEwKVU0NYH3nCk48EVwYwsUO8vGsLSaIeEEb81Ruzox5pEsjVqINbIGrQtGLYUhmwMmnYiukIz2B9Dx868dbfe5UsWq5wG/v+B/IT2o01I4DZOYrZ2zHv+nGX8LEzKTpHiuLL37z3xipwrFPYq8U5JOihOUZ7+cFuaSRCeju2QAkDvI3Z5qJGizAYpWcptyhUFI+KatlcKZMmU4hlejeh7//wldJn8NAIY+ZBy7Y+wSNgoLugR6M470VbU9AG4Tp7wuIoGqAU1+BnuF8pTgFL7zvoVFgLhWlDpQCFd4tEyZzWD3EvMmHtnhu9TIMysYmUMDAIDqWLKfSWGjOOiSJggwUwjxirjCuSrupXOwtZ55dgZptZ7/xtFPhuW3njZSqSbD8gekbXv/6kYoN563pbkeW7WRlgmBd6V+dMsjy4nkg6Udr7SQUPYWou4CE8/RJp582XHekwKsPOxTwyc+hQhxuoKGumSsSWxtK3BteOwc4cQk++8lLEdgC4sSisDcIeXGb1Uov6w0ODKCewmt9jVBp6EyOA6YYBgqc8b04plzHOOy441i6svvJTT+pXGhbJSIP+fY+pJNrFGPujNnIcUV2XNmS2iyaD56JdBUYKIAmCcNAjRw6eBpRFauDNKlUSYhmTWiW6KhBif1Q3eS/GTebTNgHccccdvTm9BFC0ydOxopVvLxNgMeWPY/7Fy1CXz6f1qhNrQyN5TwRVOOzKMZ9eODO6v6EpEFnIZ97pYj40ki57XH5tzjymFczac+69sFByMekE7IZxH19qK1thBIh1BE8NVpO71naX9za1OYWGI5R2DMgv2aFGjFFOPiBzFFcGTwX479+cS2eefiJF1fdZvzi8945nC4m7nCkQqDoA7nmejy54gWspHKLUtPW455I+Km5OCpGNUwm/XAGIpgaCjLbpyYkqnsEm5hHrTOnVVdhqNTv77+TUqcBuqGkqrzSnMZWuZ/JIKiqjhS6/5kn0dWxFkfOmoND58xBXaYWoInY29WLjjiP5qAGmSTGOy+sztw+4pjDAeMhFuRDwK6QMAVoY/Db3/1+KHn3e8s3dHIwC+hcsYgsKUAGMagbB+uCFDylsTWp2KtPEFQeuK9e/T0gR1425sD7YEQKKHJCAEI89tQjUEEOl13w/qr7YWVzNlRaKSIbClfy1ratgo4sjpo+EzPHj4cosqY5YziFBgWbrpKWq8///uU+yMGHrE5iDQheR4USf0RwwGZyLJ5eXN3EgKHnlNeeTB3SpbaH0qrx2AUWk8YJdOe9912MV3YXnnke/u+x+wBt0ch9qtCe52Rz96LnMMnLoWliHS752Ptw6y/+B9U8Cx9+AAmRUHmGi6dhmu9pwuTGutTfna/G8U2495F7MWNSLUySwavmULtjA6oz5CiyyP1DaBOamuwpZ43dSctocAeZyor0mQ9cQQFTOPENJ+A9H3w3GupzkI9Elc7gdYcdP5rmcMvvb0Vi1FZ1OHZbxbcXOXDKgZj1qgPwxnNPx2FHHY4zedT9ulOOR+uB0zF9/my86byz8OAj9+Os449HEXyoVI4HImLmKSgmjMZZjMs1jqYCaG2mrVgko6onMgKRFB4WSMUbf3SdeFXBu84+H4qLxe8fuYsoYgSDAzjrhNfgs9/4HBQPqq77XvW/zhQhA4Ot+URF8sCdJ1cB0lOilIHd4+7+y+N44KF7cOgBM6AV1xtZG53HQxPDBjUUmRTQzgi4+WMECfdJzNi7Tqm0fT8IUr+a10N3P4DrrroOhXyhmuLbLHPOG06H5r+ySadUiY5tFt5G4splK3HX7XfhqceexO233IYH7vkz1q9ehzXLV+EOxk869gQobfCTn99IrgMeTRWynmx328D2oiQpMkSO3JE56BcV2H70+l/dyEwtTUHJSRpj1bhaOcRhQUdq5U9gLMOB1Xj7e6pblVg8daeecCoU97FebR0MFehfP/OlNL3aV+IibOrqT4sblJcgQPN0lMwTsphHBg3S/mRol7sVXV046dXzMW/2oRifmwlHBUKmiIR6rLg5J3+geTRsODKaxrBDgR0u7nI69gWEHT3rSaYBZxL6u9d96PwLsXFTN2VAQaS7v5eHFFU26ViBFni6rjz83JNV1TrvTWfC90xaxyi2WVUtoKaudlhdtTFwZA84Ef/i6p3/W6cqSUiLOW44musbpGmoLQ5mtIt7YHnKI2aVNUVkVSGtsCtfS1a3YSpt9RSnpnJkIggPdbpEImWQ5qSW2sCKpRhQyDI9x8jedTEZp2mL/Piqb+8RQr754+9hYn0zHDe/RisoDowmDSGAfDp8DOxi19zYDDXAAeBE2tRQXxm7ZRFOdgln5IS0aQd0b+phYmUX8mQ2UYDoAaKAPfLB+bNixe7OjeCmGiIXCH0xogAvQX9HJ37xu1sr1t8VBRxNSsO7o8QXbLG8hkEnHCj58xKjOCGRQYrHNr+85c7hAjsbOPvc8zBj2mTQBCcTyD4qCQjyx3Y7i3tP1O9e28Vx8xBMmLDbm3thw0pcfsn70UWhUSbAuv48hQXoyA+mbftcrRccd2Qa3tWvfK3GoAfIj99XxK1ZQilOdB5M5OAVI5xy7Gtx8IJ5GOm5/6mH0JBp5EFITJ1IIFtgzqlUqq2Fcls45O/mQl/x2AasR2ElDRHNmPrJE/GOk9+M2+67fVvVdlnag4sW8nTZIskq0gto6g1oGmLo0UGmNAMpJjie6oBC/tZzTmVs17hf/fom9hxQSYKEJ0mQNoD074TovezdpClNnHOBo+ftwsvhF/Vaczl2rgjVn8BPDJomTYL8dyitNfVIlEVTrgZBPoEXadx8y80vqr3z0bsf/wvXfyBDVG1t8ucBDIzklEtzHVUp9BN0hb0MAc8/+RSKvAOMeMzvohAuSlDknqJA2MQL39fNPxbgZKBY2lcKRa5HcpLGRFR6LPfLlqZkNJiHrICWFZz2BQPVyeDghmkIXZ6pu9515Xtx3MGHcRX00vbypAXUE+lLuTWtaeTaVM/DoQ2nxvr1y/HHBx8rl9lhX+6IDJf/4sAmiF1sPI9mPwdBmKkcGSAGyw6j3yMVKQ7oXL4GKvbxwNOj/xqhEpFPr1iM3vwmgOflsw6YDfnxlCInnYzMeKwcijlBgYRvIFPhQZOnY+7hhzBn17jeYg9OOPSoIYsBGNc0uSLi9AcfOYxeaNOj9aaGcayjYUlzQHX0uIFJaOYUKFseT2QzoUGDlwXnAfD0GdAamprQ3UNzjXZKTCCCEd34iROo7A6Ch4LD+iI/Fu39fek+++BDFnASyiLhhDSnwso4YkNbZF7z65/ChQ7jZbFRHjzKrR8naN/UhciwIPP4Tp2WewQtwfLGiabd9CkteO1xR0jqTkEN90OKs0h9Xc0wHuNpHn8mw/GXeyDQCSZPn4alG9vwmrnH4v5H70dj0/gdJvuCd1+ApxY9xpm6iLXc5B8yZR5yQR0HhpKVODTy9MzwCsB5NH2M5WpIAaUSPfr8Ijyxain3Ex6eXfg47nnszztMQ7niD37xX/Ao/M74xAuaK0BzTVM5e5v+X196PnyuKJKpOJbPta3DIDzkmeZ4iCDpSaEIDjICClvEfoi2iZw9vmQRNspXBXKgRaGc3DiZJh41kvUPmD9Hqm4XrrvpeuoPJdUoUIsApbjZV5jEKwY2A1FuUdTE+ljEk8q13W141+WXYkee9o3ruEd1uPiM89HV0wuhnd2DMEhzOZzS1AQliEmOeAJMBst4JMqTOCPSMZ9dc1j6gpwelZJH+37vhz6OWl5AOmmSy2AcF3kcnEca5WrEhmCcj5f78/izT5NEi409mzjbObxuwevQ3daJPM2VMIlThstHoduDiJtrJ8BVRvxrr7kRk2dRaLhBaK1ppKmQQNNUsGSMpQKBQpYojYefX8x0B18b3PvA/Th8/nwcPuMgcF4iPR4WzD0Erljg6WeUghzLxi5EtAWEnJ2jFEIUXIGmT0jBjQgx5AcvP/D29yCXqWPLDhliHejqA0KNgrXotzGkjhPaCRHNtZB9/u9rbgAsSEaMmJPM3Ekz0i84XGc3jPxAjLMwGQ3qZlrGY+HOtavZlxAzW6egIZcDRBkcaJ1YOPbV4zH2smcWk/Y81qx7gTwdZJ8KcLaIvzx0V0rHWa8/HUvWr0JigA0DJeH2qEw+twuWFlVMSfaJ0+dJhtE+Whsm47uf/XdEXNGLpH9Z+1r8xzXfxQHzDmZPgVxtDV7L+753f+JyPPDMQohpLRCTT01NEwAdw3Ai03K5zj6I/EbsL5UFAcdr3bKlKPoqxSUv6lRMX0Nx6QLtcUawfNV6CrnCgbNaJLpDcM5fnwVYDo/zWF/D8zPI5AIwER5NPAZgOSDiv5zhgIO4gSadR8ydS+FQGDAW6ws9kI8gfXBUOXgYAURIHGff/qiIIjssXxlMytRAvh+Vo3+POIpGU5AVhThC3gNTgOMOmE+0xM+2582Zw8trcomWsPwMMRTw0NOLAC9DoWMF60GTzzIxeZycyuC7AB4V1uMa4rEFE2vi8WC4T1W8S1lHs2hgkAJLhAnR1zbXI8oCSik8+9xzpMNHQskRUDTRfLZjaWbGSQSlPU62BqGKwYKomcBVOvBR1MCA0higZdMfhswKMLFlJoCAq914+AxZXrgzA57RSGWRfSRzuDpmMXXybEDl2C8fMe2nVx9+EjK8uAeVrb62QaqhrrYeiqeGKduJwys65ACweyktKFgMdnXDTWokTg8BJ4cDGlvxqUsvx7JFz0Mmvf6Nvbjrljvw3Su/i6PmHAVqBcS8NvDhoBBTeaCp6HEePR0d0OSXUz4VU7ElD7NmH8RSDA456nEMTiJgbSZZMi7BzOnTGaajdsbWMTB698ZTTiZhRKsU5D9LFl7Jl7cJWyhjM8wrh/e2v732ayms0JqzEGAHisgqi+b6Ogx0dGPdQB9NsmhkIGOlm3VcnTPEYxFyiCh8nCUNV6kwAOTkilG4IIMcpcHLUxqVRsGUqJo0YRLki48o57CsYwMQAScf+xr6DkrKsHg6qgpsDZAhE5BxTZgn/IfkeAqWE4FTCRXMobWmDrWcmSkvMCxsk4ij4xCw8tFz50G+DQSKrGlTASbhcErxsMqXIKhTSCjIEVcqONLKemwOGRJVy33SONRw5QbblDxCAsqmg2IdqyikbC1hx+V/SLFyl8i+yBfyeQUUWFzTNswHHlYAoekAAAAD/ElEQVRJn4m4paExzahlZxUvQFmMCuXBZjIUfM3UGF39G1HMJchMrEejZn99CxtQrv0EsUqQqBjyK6nIOGQaAuSiCIHHfMaLJiFrFfH48DgBgYSNNzWYwMOfvAMnDg0fBsLTRCGVCQw9nFey4JiBEw+TPBbzwfZBbEzLwqgiLv/o32O0z8b2TsAAmhUDMk58TVIMSUH5kcRy+GXqG2GEB1A2oGsz7JLwKEBt83i01tezN/7IkDITEDQgN7QKoMl1aAMYw3kaIAIoiTJIFgE5BjykHqh8zEKGCHzCvNZWMCKOvmLBrZ1iEqtINZRxCv8N62qU/inJELqMFEZaFop5xgelAZA8xkFaDVvyoJHS5QHG98gDlgXgG5BGDZ+mlBQB6/nQaVF6kM7lFNKg9DHNYBmlFNM08RgY8sADywjRCmwfxEkYkhlhxYyWyQDzNPhkCUKbL/UZNiAupKgN300NE0ixT9weYDzm6RRMmlJ6a5YrA7jSgCUMQUbXZ0MGfDTBJ1CRNfNq2aYkMQijkAIM84dcmjcU3o7n41+/8vnt5G0/uaW5mVPR9vPHcsY4sD9xoKIiRVbBM4OIaJOOpuNr29ZCtHc0dcbKjnFgX+VARUVSXIoDKVXoxX9+/7qq+zmlZQrL0rDke8yNcWB/54CoyIh95HYNvtcAT2dw+WWVf11zMzLaqJsjOxIaqzPGgX2GAxUVKT2q5PEteHqlvRh5npZU07uly5cD3FRi7BnjwCuAAxUVyVc+Vq5tA09lMZgvImtCRFUo0/33PwBem7wCWDjWxTEOVHMcEDpMm97C83Ugk2vAYP8mdHUsxV+eWDwi/95/0bugzYhFxjLHOLDfcKDiioSMSo/Lszy1U1xiauomobnlIBz1qgPx29/+FiM9cgFWOgO3abE4jlO//Ep4IVkOj/ljHNiXOVBRkRwVSDqolIJSSoIp+LzIOvnkkzFpMi/L0pSXvtavb0/NuyQpnd5puXRjsbICGV7GMTrmxjiwz3OgakWSniqlUFYsiWezWbS3rcFlH7hCoi+BGbyFF+URkEylSoooOCxXN0kbgzEO7A8cqKhISpWEv9xZUQKBcrxnQx9++IOv4yvf+lY5aStfjDr5PksUp1xPViKltsa7VaWxyBgH9jEOjFqRlFI81S6tTKIcjZPHQT5Y/NuPfQTX/uyGl3T/uutvkGxgyKwDbFpfKYXkJaXHEsY4sG9yoKIivbhbSqk0SSmVKkRCxYjiBCoELv2b8/HI40+m+eXXuy++CPEWeyRHk87ZrQ8dymXH/DEO7D0O7FzLo1akLZtTSlFJDIzvoDwFWI0jD5mPoitsWQzWMS9NselbqVK89E6Txl5jHNinOfD/AQAA///NSWuBAAAABklEQVQDAEZTOm7pRG5AAAAAAElFTkSuQmCC" x="0" y="0" width="210" height="79"/>
</svg>
<br/>
<p class="tp-company-name"><?php echo $company_info[0]['company_name']; ?></p>
        <p class="tp-company-sub"><?php echo $company_info[0]['address']; ?></p>
        <p class="tp-company-sub"><?php echo $company_info[0]['mobile']; ?></p>
        <?php if (!empty($company_info[0]['email'])): ?>
            <p class="tp-company-sub"><a href="mailto:<?php echo $company_info[0]['email']; ?>"><?php echo $company_info[0]['email']; ?></a></p>
        <?php endif; ?>
        <?php if (!empty($company_info[0]['website'])): ?>
            <p class="tp-company-sub"><?php echo $company_info[0]['website']; ?></p>
        <?php endif; ?>
    </div>

    <hr class="tp-divider">

    <!-- ── INVOICE INFO ── -->
    <table class="tp-info-table">
        <tr>
            <td class="tp-info-label">Invoice No</td>
            <td class="tp-info-colon">:</td>
            <td class="tp-info-value"><?php echo $invoiceno; ?></td>
        </tr>
        <tr>
            <td class="tp-info-label">Invoice Date</td>
            <td class="tp-info-colon">:</td>
            <td class="tp-info-value"><?php
                date_default_timezone_set('Asia/Colombo');
                echo date('d F Y', strtotime($date)) . ' ' . date('h:i A');
            ?></td>
        </tr>
        <?php if (!empty($users_name)): ?>
        <tr>
            <td class="tp-info-label">User</td>
            <td class="tp-info-colon">:</td>
            <td class="tp-info-value"><?php echo $users_name; ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <br>

    <!-- ── CUSTOMER INFO ── -->
    <table class="tp-info-table">
        <?php if (!empty($customer_name)): ?>
        <tr>
            <td class="tp-info-label">Customer</td>
            <td class="tp-info-colon">:</td>
            <td class="tp-info-value"><?php echo $customer_name; ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($customer_address)): ?>
        <tr>
            <td class="tp-info-label">Address</td>
            <td class="tp-info-colon">:</td>
            <td class="tp-info-value"><?php echo $customer_address; ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($customer_mobile)): ?>
        <tr>
            <td class="tp-info-label">Mobile</td>
            <td class="tp-info-colon">:</td>
            <td class="tp-info-value"><?php echo $customer_mobile; ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <hr class="tp-divider">

    <!-- ── PRODUCTS TABLE ── -->
    <table class="tp-items-table">
        <thead class="tp-items-thead">
            <tr>
                <th class="tp-th-product">Product</th>
                <th class="tp-th-price">Price</th>
                <th class="tp-th-qty">Qty</th>
                <th class="tp-th-total">Total</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($invoice_all_data as $item): ?>
            <tr class="tp-item-name">
                <td colspan="4"><b><?php echo $item['product_name']; ?></b></td>
            </tr>
            <tr class="tp-item-vals">
                <td></td>
                <td class="tp-item-price"><?php echo number_format((float)$item['product_rate'], 2, '.', ','); ?></td>
                <td class="tp-item-qty"><?php echo $item['quantity'] . ' ' . $item['unit_name']; ?></td>
                <td class="tp-item-total"><?php echo number_format((float)$item['total_price'], 2, '.', ','); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <hr class="tp-divider">

    <!-- ── TOTALS ── -->
    <table class="tp-totals-table">
        <tr>
            <td class="tp-totals-label">Total:</td>
            <td class="tp-totals-value"><?php echo number_format((float)$total, 2, '.', ','); ?></td>
        </tr>
        <tr>
            <td class="tp-totals-label">Total Discount:</td>
            <td class="tp-totals-value"><?php echo number_format((float)$total_discount_ammount, 2, '.', ','); ?></td>
        </tr>
        <?php if (!empty($total_vat_amnt) && (float)$total_vat_amnt != 0): ?>
        <tr>
            <td class="tp-totals-label">Total VAT:</td>
            <td class="tp-totals-value"><?php echo number_format((float)$total_vat_amnt, 2, '.', ','); ?></td>
        </tr>
        <?php endif; ?>
        <tr class="tp-grand-total">
            <td class="tp-totals-label"><b>Grand Total:</b></td>
            <td class="tp-totals-value"><b><?php echo number_format((float)$grandTotal, 2, '.', ','); ?></b></td>
        </tr>
    </table>

    <hr class="tp-divider">

    <!-- ── THANK YOU ── -->
    <p class="tp-thankyou">Thank You, Come Again!</p>

    <!-- ── TERMS & CONDITIONS ── -->
    <?php
    $CI =& get_instance();
    $ws = $CI->db->select('terms_conditions')->from('web_setting')->get()->row();
    $raw_terms = (!empty($ws) && !empty($ws->terms_conditions)) ? trim($ws->terms_conditions) : '';
    $terms_lines = !empty($raw_terms)
        ? array_filter(explode("\n", $raw_terms), function($l){ return trim($l) !== ''; })
        : [];
    if (!empty($terms_lines)): ?>
        <p class="tp-terms-title">Terms &amp; Conditions</p>
        <ul class="tp-terms-list">
            <?php foreach ($terms_lines as $line): ?>
                <li><?php echo htmlspecialchars(trim($line)); ?></li>
            <?php endforeach; ?>
        </ul>
        <br>
    <?php endif; ?>

    <hr class="tp-divider">

    <!-- ── POWERED BY ── -->
    <p class="tp-poweredby">Powered by:</p>
    <p class="tp-poweredby"><b>Fexten Solutions (Pvt) Ltd.</b></p>

</div><!-- /.tp-receipt -->
</div><!-- /#tp-root -->

<!-- ── QZ TRAY STATUS BAR ── -->
<div id="qz-status-bar" class="tp-no-print" style="
    position: fixed; bottom: 0; left: 0; right: 0;
    background: #222; color: #fff; font-family: Arial, sans-serif;
    font-size: 13px; padding: 10px 18px;
    display: flex; align-items: center; justify-content: space-between; z-index: 9999;">
    <span id="qz-status-msg">⏳ Connecting to QZ Tray...</span>
    <div>
        <button onclick="qzPrint()" style="margin-right:8px; padding:5px 14px; background:#28a745; color:#fff; border:none; border-radius:4px; cursor:pointer;">🖨 Print Again</button>
        <button onclick="window.print()" style="padding:5px 14px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer;">🌐 Browser Print</button>
    </div>
</div>

<script src="<?php echo base_url('assets/js/qz-tray.js'); ?>"></script>
<script>
var QZ_PRINTER_NAME = 'XP-Q838L'; // change if printer name differs in your OS

function qzSetStatus(msg, color) {
    var el = document.getElementById('qz-status-msg');
    if (el) { el.textContent = msg; el.style.color = color || '#fff'; }
}

// Allow unsigned connections (localhost)
qz.security.setCertificatePromise(function(resolve) { resolve(); });
qz.security.setSignatureAlgorithm('SHA512');
qz.security.setSignaturePromise(function() {
    return function(resolve) { resolve(); };
});

function qzPrint() {
    qzSetStatus('⏳ Connecting to QZ Tray...', '#fff');

    var connectPromise = qz.websocket.isActive()
        ? Promise.resolve()
        : qz.websocket.connect();

    connectPromise
    .then(function() {
        qzSetStatus('🔍 Finding printer...', '#fff');
        return qz.printers.find(QZ_PRINTER_NAME);
    })
    .then(function(printer) {
        qzSetStatus('🖨 Printing to: ' + printer, '#8fffb0');

        var cfg = qz.configs.create(printer, {
            size: { width: 80, height: null },
            units: 'mm',
            scaleContent: true,
            rasterize: true,
            density: 203  // XP-Q838L DPI
        });

        // Build self-contained receipt HTML
        var styleTag = document.querySelector('#tp-root style');
        var styles   = styleTag ? styleTag.innerHTML : '';
        var body     = document.getElementById('tp-root').innerHTML;

        var html = '<!DOCTYPE html><html><head>'
            + '<meta charset="UTF-8">'
            + '<style>' + styles + '</style>'
            + '</head><body style="margin:0;padding:0;background:#fff;">'
            + body
            + '</body></html>';

        var data = [{ type: 'pixel', format: 'html', flavor: 'plain', data: html }];
        return qz.print(cfg, data);
    })
    .then(function() {
        qzSetStatus('✅ Printed successfully!', '#8fffb0');
    })
    .catch(function(err) {
        console.error('QZ Tray error:', err);
        if (err.message && err.message.indexOf('Unable to establish') !== -1) {
            qzSetStatus('❌ QZ Tray not running — using browser print instead', '#ff8080');
            setTimeout(function() { window.print(); }, 800);
        } else {
            qzSetStatus('⚠️ Error: ' + err.message, '#ff8080');
        }
    });
}

// Auto-print on page load
window.addEventListener('load', function() {
    setTimeout(qzPrint, 600);
});
</script>
