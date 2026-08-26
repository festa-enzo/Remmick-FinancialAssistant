    const conversoes = {

        categories: {
        1: "Alimentação",
        2: "Transporte",
        3: "Casa",
        4: "Compras",
        5: "Lazer",
        6: "Saúde",
        7: "Educação",
        8: "Tecnologia",
        9: "Serviços",
        10: "Roupas",
        11: "Presente",
        12: "Outros"
    },

    institution: {

        1: "Nubank",
        2: "Itau",
        3: "Bradesco",
        4: "Banco_do_Brasil",
        5: "Caixa",
        6: "Santander",
        7: "Inter",
        8: "C6",
        9: "BTG",
        10: "XP",
        11: "Mercado_Pago",
        12: "Pagbank",
        13: "Neon",
        14: "Picpay",
        15: "Sicredi"
    },

    method: {
        1: "Dinheiro",
        2: "Débito",
        3: "Crédito",
        4: "Boleto",
        5: "Pix",
        6: "Transferencia",
        7: "Vale_Refeicao",
        8: "Vale_Alimentacao",
    },

    is_paid: {
        0: "Não Pago",
        1: "Pago"
    },

    is_received: {
        0: "Pendente",
        1: "Recebido"
    },

    income_origin: {
        1: "Empresa",
        2: "Cliente",
        3: "Loja",
        4: "Banco",
        5: "Outros"
    },

    income_type: {
        1: "Salário",
        2: "Freelance",
        3: "Vendas",
        4: "Investimentos",
        5: "Outros"
    }    
}
export function converterNumero(tipo, id) {

    if(!conversoes[tipo]) {
            return null;
        }
        if(!conversoes[tipo][id]){
            return null;
        }
    return conversoes[tipo][id];
}
