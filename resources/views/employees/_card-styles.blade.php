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
        border-radius: 3.2cqw;
        background: #f8fbfa;
        color: #0c2332;
        font-family: Inter, "DejaVu Sans", Arial, sans-serif;
        box-shadow: 0 1.2rem 2.8rem rgba(12, 35, 50, .13);
        isolation: isolate;
    }
    .employee-card__template {
        position: absolute;
        z-index: 1;
        inset: 0;
        display: block;
        width: 100%;
        height: 100%;
    }
    .employee-card__foreground {
        position: absolute;
        z-index: 3;
        inset: 0;
        display: block;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }
    .employee-card__copy {
        position: absolute;
        z-index: 4;
        left: 4.8%;
        top: 36.2%;
        width: 49%;
        color: #0c2332;
    }
    .employee-card__copy h1 {
        display: -webkit-box;
        height: 7.6cqw;
        margin: 0;
        overflow: hidden;
        font-size: 2.75cqw;
        font-weight: 800;
        line-height: 1.12;
        letter-spacing: -.03cqw;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }
    .employee-card__position {
        height: 6.8cqw;
        margin: 0;
        padding-top: .8cqw;
        overflow: hidden;
        font-size: 2.85cqw;
        line-height: 1.15;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .employee-card__code {
        margin: .45cqw 0 0;
        font-size: 2.65cqw;
        font-weight: 750;
        line-height: 1;
    }
    .employee-card__portrait-window {
        position: absolute;
        z-index: 2;
        top: 0;
        right: 0;
        width: 56%;
        height: 100%;
        overflow: hidden;
    }
    .employee-card__portrait {
        position: absolute;
        width: auto;
        max-width: none;
        transform: translate(-50%, -50%);
        object-fit: contain;
        filter: drop-shadow(0 .4cqw .65cqw rgba(7, 32, 43, .14));
    }
    .employee-card__portrait-empty {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        color: rgba(255, 255, 255, .5);
        font-size: 2.1cqw;
        font-weight: 800;
        letter-spacing: .28cqw;
    }
    .employee-card__signature {
        position: absolute;
        z-index: 3;
        left: 7.3%;
        bottom: 9.3%;
        width: 47.5%;
        height: 15.5%;
        object-fit: contain;
    }
    .employee-card__qr {
        position: absolute;
        z-index: 3;
        right: 8.55%;
        bottom: 3.85%;
        width: 18.4%;
        aspect-ratio: 1;
    }
    .employee-card__qr-pending {
        position: absolute;
        z-index: 3;
        right: 8.55%;
        bottom: 3.85%;
        display: grid;
        width: 18.4%;
        aspect-ratio: 1;
        place-items: center;
        color: rgba(8, 127, 115, .28);
        font-size: 4.4cqw;
        font-weight: 800;
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
