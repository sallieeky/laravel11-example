import { ElMessageBox } from "element-plus";
import { h } from "vue";

export const dd = (...data) => {
    if (import.meta.env.VITE_APP_DEBUG === "false" || import.meta.env.VITE_APP_DEBUG === false || import.meta.env.VITE_APP_ENV === "production") {
        console.warn("Debugging is disabled.");
        return;
    }

    ElMessageBox({
        title: "Debug Dump",
        dangerouslyUseHTMLString: true,
        customStyle: {
            overflow: "auto",
            width: "90vw",
            maxWidth: "90vw",
            height: "90vh",
        },
        message: h("div", {
            style: {
                backgroundColor: "white",
                color: "#56DB3A",
                fontWeight: "bold",
                fontSize: ".9em",
                width: "calc(90vw - 2rem)",
            },
            innerHTML: data.map((d) => {
                return `<div style="background-color:#18171B; padding:.2rem">` + parseData(d) + "</div>";
            })
        }),
        showCancelButton: false,
        showConfirmButton: false,
        closeOnClickModal: true,
    });

    throw new Error("Debug Dump");
}

const quote = (data) => {
    return `<span style="color: #FF8400">"</span>${data}<span style="color: #FF8400">"</span>`;
}

const parseData = (data) => {
    if (typeof data === "string") {
        if (data.length > 100) {
            return `
            <span>
                <span style="color: #FF8400">"</span>${data.substring(0, 100)}
                <div style="display: none;">${data.substring(100)}<span style="color: #FF8400">"</span></div>
                <button style="display: inline-block; color: #A0A0A0; border: none; cursor: pointer;" onclick="this.previousElementSibling.style.display = this.previousElementSibling.style.display === 'none' ? 'inline' : 'none'; this.innerHTML = this.innerHTML === '▶' ? '▼' : '▶';">▶</button>
            </span>

            <span style="color: #A0A0A0;">// string (${data.length})</span>`;
        }
        return `<span>${quote(data)}</span> <span style="color: #A0A0A0;">// string (${data.length})</span>`;
    } else if (data === null) {
        return `<span style="color:#FF8400">null</span>`;
    } else if (data === undefined) {
        return `<span style="color:#FF8400">undefined</span>`;
    } else if (typeof data === "number") {
        return `<span>${data}</span> <span style="color: #A0A0A0;">// number</span>`;
    } else if (typeof data === "function") {
        return `<span style="color:#1299DA">function</span> <span style="color:#FF8400">{</span>${data.name}<span style="color:#FF8400">}</span>`;
    } else if (typeof data === "boolean") {
        return `<span>${data}</span> <span style="color:#A0A0A0;">// boolean</span>`;
    } else if (Array.isArray(data)) {
        let html = `<span style="color:#1299DA">array` + `: ${data.length} </span> <span style="color:#FF8400">[</span>`;
        html += `<button style="color: #A0A0A0; border: none; cursor: pointer;" onclick="this.closest('div').querySelector('div').style.display = this.closest('div').querySelector('div').style.display === 'none' ? 'block' : 'none'; this.innerHTML = this.innerHTML === '▶' ? '▼' : '▶';">▼</button>`;
        html += `<div>`;
        data.forEach((d) => {
            html += `<div style="margin-left: 1rem">` +
                `<span style="color:#1299DA; font-size:.9em">[` +
                data.indexOf(d) + `] : </span>` +
                parseData(d) + `</div>`;
        });
        html += `</div>`;
        html += `<span style="color:#FF8400">]</span>`;
        return html;
    } else if (typeof data === "object") {
        let keysLength = Object.keys(data).length;
        let html = `<span style="color:#1299DA">object: ${keysLength}</span> <span style="color:#FF8400">{</span>`;
        html += `<button style="color: #A0A0A0; border: none; cursor: pointer;" onclick="this.closest('div').querySelector('div').style.display = this.closest('div').querySelector('div').style.display === 'none' ? 'block' : 'none'; this.innerHTML = this.innerHTML === '▶' ? '▼' : '▶';">▼</button>`;
        html += `<div>`;
        for (let key in data) {
            html += `<div style="margin-left: 1rem"><span style="color:#FF8400">"${key}" : </span> ` + parseData(data[key]) + `</div>`;
        }
        html += `</div>`;
        html += `<span style="color:#FF8400">}</span>`;
        return html;
    } else {
        return `<span style="color:#FF8400">unknown</span>`;
    }
}
