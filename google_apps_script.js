// Google Apps Script para receber dados e salvar na planilha
// Copie este código e cole no Google Apps Script (script.google.com)

function doPost(e) {
  try {
    // Recebe os dados do POST
    var data = JSON.parse(e.postData.contents);
    var spreadsheetId = data.spreadsheet_id;
    var formData = data.data;
    
    // Abre a planilha
    var spreadsheet = SpreadsheetApp.openById(spreadsheetId);
    var sheet = spreadsheet.getActiveSheet();
    
    // Prepara os dados para inserir
    var rowData = [
      formData.data,
      formData.hora,
      formData.nome,
      formData.telefone,
      formData.email,
      formData.unidade,
      formData.estado,
      formData.cidade,
      formData.localidade_url,
      formData.campanha,
      formData.origem_clique,
      formData.url_completa,
      formData.ip,
      formData.user_agent
    ];
    
    // Adiciona a nova linha
    sheet.appendRow(rowData);
    
    // Retorna sucesso
    return ContentService
      .createTextOutput(JSON.stringify({
        'success': true,
        'message': 'Dados salvos com sucesso',
        'row': sheet.getLastRow()
      }))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
    // Retorna erro
    return ContentService
      .createTextOutput(JSON.stringify({
        'success': false,
        'message': 'Erro: ' + error.toString()
      }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

function doGet(e) {
  return ContentService
    .createTextOutput('API funcionando!')
    .setMimeType(ContentService.MimeType.TEXT);
}

// Função para configurar a planilha (execute uma vez)
function setupSpreadsheet() {
  var spreadsheetId = '1W9OAplQEFABJFWlbhNbhw4bwwuBCXZWw6upQ5lCSx0Q';
  var spreadsheet = SpreadsheetApp.openById(spreadsheetId);
  var sheet = spreadsheet.getActiveSheet();
  
  // Cabeçalhos das colunas
  var headers = [
    'Data',
    'Hora',
    'Nome',
    'Telefone',
    'Email',
    'Unidade',
    'Estado',
    'Cidade',
    'Localidade URL',
    'Campanha',
    'Origem Clique',
    'URL Completa',
    'IP',
    'User Agent'
  ];
  
  // Limpa a planilha e adiciona os cabeçalhos
  sheet.clear();
  sheet.getRange(1, 1, 1, headers.length).setValues([headers]);
  
  // Formata os cabeçalhos
  sheet.getRange(1, 1, 1, headers.length)
    .setFontWeight('bold')
    .setBackground('#4285f4')
    .setFontColor('white');
  
  // Ajusta a largura das colunas
  sheet.autoResizeColumns(1, headers.length);
  
  Logger.log('Planilha configurada com sucesso!');
} 