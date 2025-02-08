function mensagem(icone,mensagem)
{
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.onmouseenter = Swal.stopTimer;
          toast.onmouseleave = Swal.resumeTimer;
        }
      });
      Toast.fire({
        icon: icone,
        title: mensagem
      });
}


function getQueryParams() {
  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);

  let params = {};


  for (const [key, value] of urlParams.entries()) {
      params[key] = value;
  }

  return params;
}

function aplicarTema(tema) {
  if (tema === 'dark') {
    document.body.style.backgroundColor = '#121212';
    document.body.style.color = '#ffffff';

    document.querySelectorAll('div, table, th, td, p, span, a, button, input, textarea').forEach(element => {
        element.style.backgroundColor = '#1c1c1c';
        element.style.color = '#e0e0e0';
        element.style.borderColor = '#333';

        if (element.tagName === 'A') {
            element.style.color = '#1e90ff';
        } else if (element.tagName === 'BUTTON') {
            element.style.backgroundColor = '#333';
            element.style.color = '#ffffff';
        } else if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
            element.style.backgroundColor = '#333';
            element.style.color = '#ffffff';
            element.style.border = '1px solid #555';
        }
    });

    // Forçar estilo para linhas alternadas da tabela, sobrepondo o estilo do Bootstrap
    document.querySelectorAll('table.table-striped tbody tr:nth-child(odd) td').forEach(cell => {
        cell.style.backgroundColor = '#444 !important'; // Cor para células de linhas ímpares
    });

    document.querySelectorAll('table.table-striped tbody tr:nth-child(even) td').forEach(cell => {
        cell.style.backgroundColor = '#2a2a2a !important'; // Cor para células de linhas pares
    });

    // Estilizar cabeçalhos de tabela (<th>) para destacar
    document.querySelectorAll('table th').forEach(header => {
        header.style.backgroundColor = '#333';
        header.style.color = '#ffffff';
        header.style.border = '1px solid #555';
    });
  }
}


function convertInputsToUppercase() {
  const inputs = document.querySelectorAll('input[type="text"]');
  
  inputs.forEach(input => {
      input.addEventListener('input', () => {
          input.value = input.value.toUpperCase();
      });
  });
}

document.addEventListener('DOMContentLoaded', convertInputsToUppercase);