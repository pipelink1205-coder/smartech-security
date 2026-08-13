<div style="display:flex;justify-content:center;align-items:center;background:#334155;border-radius:12px;padding:16px;min-height:75vh;">
    <iframe
        src="{{ $url }}#toolbar=1&navpanes=0&scrollbar=1&view=FitH&zoom=page-fit"
        title="{{ $title ?? 'Vista previa PDF' }}"
        style="width:min(100%, calc(75vh * 0.72));height:75vh;border:0;border-radius:8px;background:#525659;box-shadow:0 12px 40px rgba(0,0,0,.35);"
    ></iframe>
</div>
