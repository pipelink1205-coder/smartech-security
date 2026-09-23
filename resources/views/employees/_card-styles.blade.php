<style>
    .employee-card-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(320px, 1fr));
        gap: 1.5rem;
        align-items: start;
    }
    .employee-card {
        container-type: inline-size;
        position: relative;
        width: 100%;
        aspect-ratio: 85.6 / 54;
        overflow: hidden;
        border: 1px solid rgba(12, 35, 50, .08);
        border-radius: 3.2cqw;
        box-sizing: border-box;
        background: #fff;
        color: #0c2332;
        font-family: Inter, "DejaVu Sans", Arial, sans-serif;
        box-shadow: 0 1.2rem 2.8rem rgba(12, 35, 50, .13);
        isolation: isolate;
    }
    .employee-card *,
    .employee-card *::before,
    .employee-card *::after { box-sizing: border-box; }
    .employee-card__watermark {
        position: absolute;
        z-index: 1;
        left: 34%;
        top: 1%;
        width: 52.5%;
        height: auto;
        object-fit: contain;
        opacity: .045;
        pointer-events: none;
    }
    .employee-card__watermark--back {
        left: 42.5%;
        top: 2%;
        width: 51.5%;
        height: auto;
        opacity: .042;
    }
    .employee-card__brand {
        position: absolute;
        z-index: 4;
        left: 4.8%;
        top: 7.9%;
        width: 52%;
        height: 22%;
    }
    .employee-card__brand-mark {
        position: absolute;
        left: 0;
        top: 0;
        width: 20.25%;
        height: auto;
        object-fit: contain;
    }
    .employee-card__brand-name,
    .employee-card__brand-company {
        position: absolute;
        left: 25.8%;
        white-space: nowrap;
    }
    .employee-card__brand-name {
        top: 15%;
        font-size: 3.95cqw;
        font-weight: 800;
        letter-spacing: .22cqw;
        line-height: 1;
    }
    .employee-card__brand-company {
        top: 61%;
        color: #0d7469;
        font-size: 2.35cqw;
        font-weight: 800;
        letter-spacing: .42cqw;
        line-height: 1;
    }
    .employee-card__portrait-window {
        position: absolute;
        z-index: 2;
        top: 5.5%;
        right: 0;
        width: 53.5%;
        height: 87%;
        overflow: hidden;
    }
    .employee-card__portrait {
        position: absolute;
        width: auto;
        max-width: none;
        transform: translate(-50%, -50%);
        object-fit: contain;
        filter: drop-shadow(0 .4cqw .65cqw rgba(7, 32, 43, .13));
    }
    .employee-card__portrait-empty {
        position: absolute;
        right: 12%;
        bottom: 16%;
        display: grid;
        width: 38%;
        aspect-ratio: 1;
        place-items: center;
        border: .22cqw dashed rgba(13, 116, 105, .22);
        border-radius: 50%;
        color: rgba(13, 116, 105, .35);
        font-size: 2.1cqw;
        font-weight: 800;
        letter-spacing: .28cqw;
    }
    .employee-card__copy {
        position: absolute;
        z-index: 4;
        left: 4.8%;
        top: 42.2%;
        width: 54%;
        color: #0c2332;
    }
    .employee-card__copy h1 {
        display: -webkit-box;
        max-height: 7.2cqw;
        margin: 0;
        overflow: hidden;
        font-size: 3.15cqw;
        font-weight: 800;
        line-height: 1.08;
        letter-spacing: -.05cqw;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }
    .employee-card__position {
        max-width: 85%;
        margin: 1.15cqw 0 0;
        overflow: hidden;
        color: #0d7469;
        font-size: 2.75cqw;
        font-weight: 700;
        line-height: 1.05;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .employee-card__code {
        margin: 1.15cqw 0 0;
        font-size: 2.55cqw;
        line-height: 1;
    }
    .employee-card__code strong { font-weight: 850; }
    .employee-card__authorization {
        display: inline-block;
        margin: 2.8cqw 0 0;
        padding: 1.15cqw 2.25cqw 1.05cqw;
        border-radius: 999px;
        background: #168f82;
        box-shadow: 0 .4cqw .9cqw rgba(22, 143, 130, .2);
        color: #fff;
        font-size: 2.45cqw;
        font-weight: 800;
        letter-spacing: .03cqw;
        line-height: 1;
    }
    .employee-card__back-copy {
        position: absolute;
        z-index: 3;
        left: 5.4%;
        top: 10.4%;
        width: 62%;
    }
    .employee-card__back-copy h2 {
        margin: 0;
        font-size: 3.15cqw;
        font-weight: 800;
        letter-spacing: .02cqw;
        line-height: 1;
    }
    .employee-card__back-rule {
        width: 25%;
        height: .31cqw;
        margin-top: 1.6cqw;
        background: #168f82;
    }
    .employee-card__nit {
        margin: 2.25cqw 0 0;
        font-size: 2.75cqw;
        line-height: 1;
    }
    .employee-card__notice {
        margin: 3.05cqw 0 0;
        font-size: 2.2cqw;
        line-height: 1.43;
    }
    .employee-card__contacts { margin-top: 1.55cqw; }
    .employee-card__contacts p {
        margin: 0 0 .72cqw;
        font-size: 2.08cqw;
        font-weight: 650;
        line-height: 1.25;
        white-space: nowrap;
    }
    .employee-card__contacts span {
        display: inline-grid;
        width: 3.25cqw;
        height: 3.25cqw;
        margin-right: 1.15cqw;
        place-items: center;
        border-radius: 50%;
        background: #168f82;
        color: #fff;
        font-size: 1.75cqw;
        line-height: 1;
        vertical-align: middle;
    }
    .employee-card__signature-block {
        position: absolute;
        z-index: 3;
        left: 5.4%;
        bottom: 16.8%;
        width: 50%;
        height: 14%;
        border-bottom: .18cqw solid #0c2332;
        text-align: center;
    }
    .employee-card__signature {
        position: absolute;
        left: 7%;
        bottom: .5cqw;
        width: 86%;
        height: 90%;
        object-fit: contain;
    }
    .employee-card__signature-block span {
        position: absolute;
        left: 0;
        top: calc(100% + .75cqw);
        width: 100%;
        color: #0d7469;
        font-size: 2.05cqw;
        line-height: 1;
    }
    .employee-card__qr-panel {
        position: absolute;
        z-index: 3;
        right: 5.4%;
        top: 23.5%;
        width: 26.5%;
        text-align: center;
    }
    .employee-card__qr-frame {
        position: relative;
        display: block;
        width: 100%;
        aspect-ratio: 1;
        overflow: hidden;
        border: .35cqw solid #168f82;
        border-radius: 2cqw;
        background: #fff;
    }
    .employee-card__qr {
        position: absolute;
        inset: 2.5%;
        display: block;
        width: 95%;
        height: 95%;
        object-fit: contain;
    }
    .employee-card__qr-pending {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        color: rgba(8, 127, 115, .35);
        font-size: 4.4cqw;
        font-weight: 850;
    }
    .employee-card__qr-panel p {
        margin: 1.15cqw -2cqw 0;
        font-size: 1.85cqw;
        line-height: 1.2;
    }
    .employee-card__footer {
        position: absolute;
        z-index: 5;
        right: 0;
        bottom: 0;
        left: 0;
        height: 7.2%;
        overflow: hidden;
        background: #0c2332;
    }
    .employee-card__footer > span {
        position: absolute;
        left: -2%;
        top: 0;
        width: 20%;
        height: 100%;
        transform: skewX(38deg);
        background: #168f82;
    }
    .employee-card__footer--back p {
        position: relative;
        z-index: 1;
        margin: 0;
        color: #fff;
        font-size: 2.2cqw;
        line-height: 3.85cqw;
        text-align: center;
    }
    .employee-card-face-label {
        margin: 0 0 .35rem;
        color: #0b6b5f;
        font-size: .72rem;
        font-weight: 750;
        letter-spacing: .08em;
        text-transform: uppercase;
    }
    @media (max-width: 900px) {
        .employee-card-grid { grid-template-columns: 1fr; }
    }
</style>
