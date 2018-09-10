**Account Code**  
Código de conta para asterisk FreePBX.  
Permite a criação de código de conta e senha que pode ser usado nas rotas de saída.

**Instalação**  
Faça o download da [última versão do módulo](https://github.com/mrpbueno/accountcode/releases) 
e realize a instalação conforme [documentação do FreePBX](https://wiki.freepbx.org/display/FPG/Download+and+Installing+Custom+Modules)

**Configuração**  
1. Cadastre as regras de acesso. Por exemplo, local, longa distância, celular, internacional;  
2. Cadastre os códigos de conta definindo as regras de acesso por usuário;  
3. Nas rotas de saída selecione a regra de acesso;  

**Utilização**  
O usuário cadastrado que recebeu o código de conta 12345 e senha padrão 4567 vai realizar chamadas externas digitando    
o número externo e quando solicitado a senha vai digitar 12345*4567#  
A chamada será completada se:  
- o código de conta estiver ativo;  
- a senha corresponde ao código de conta;  
- o código de conta tem a regra de acesso da rota de saída;

Para trocar a senha digite *11 e siga as instruções. Obs. A senha deve ter no máximo 4 digitos.