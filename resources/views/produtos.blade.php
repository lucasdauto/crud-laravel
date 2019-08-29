@extends('layout.app', ["current" => "produtos" ])

@section('body')
    <div class="card border">
        <div class="card-body">
            <h5 class="card-title">Cadastro de Produtos</h5>
            <table class="table table-ordered table-hover" id="tabelaProdutos">
                <thead>
                <tr>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>Estoque</th>
                    <th>Preço</th>
                    <th>Departamento</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <button class="btn btn-sm btn-primary" role="button" onclick="novoProduto()">Nova produto</button>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" id="dlgProdutos">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="form-horizontal" id="formProduto">
                    <div class="modal-header">
                        <h5 class="modal-title">Novo Produto</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id">
                        <div class="form-group">
                            <label for="nomeProduto" class="control-label">Nome do Produto</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="nomeProduto" placeholder="Nome do produto">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="precoProduto" class="control-label">Preço do Produto</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="precoProduto"
                                       placeholder="Preço do produto">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="estoqueProduto" class="control-label">Estoque do Produto</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="estoqueProduto"
                                       placeholder="Quantidade do produto">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="categoriaProduto" class="control-label">Categoria do Produto</label>
                            <div class="input-group">
                                <select class="form-control" id="categoriaProduto">

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                        <button type="cancel" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        //Função que vai carregar o token
        //Isso é feito para poder fazer requisição de get e post via ajax
        $.ajaxSetup({
            headers: {
                'X-CRSF-TOKEN': "{{ csrf_token() }}"
            }
        });

        function novoProduto() {
            $('#nomeProduto').val('');
            $('#precoProduto').val('');
            $('#estoqueProduto').val('');
            $('#id').val('');

            $('#dlgProdutos').modal('show')
        }

        function carregarCategorias() {
            $.getJSON('/api/categorias', function (data) {
                for (i = 0; i < data.length; i++) {
                    opcao = '<option value ="' + data[i].id + '">' + data[i].nome + '</option>';

                    $('#categoriaProduto').append(opcao);
                }
            })
        }

        function montarLinha(produto) {
            var linha = "<tr>" +
                "<td>" + produto.id + "</td>" +
                "<td>" + produto.nome + "</td>" +
                "<td>" + produto.estoque + "</td>" +
                "<td>" + produto.preco + "</td>" +
                "<td>" + produto.categoria_id + "</td>" +
                "<td>" +
                "<button class='btn btn-sm btn-primary' onclick='editar(" + produto.id + ")'>Editar</button>" +
                "<button class='btn btn-sm btn-danger' onclick='remover(" + produto.id + ")'>Apagar</button>" +
                "</td>" +
                "</tr>"

            return linha;
        }

        function editar(id) {
            $.getJSON('/api/produtos/' + id, function (data) {
                $('#nomeProduto').val(data.nome);
                $('#precoProduto').val(data.preco);
                $('#estoqueProduto').val(data.estoque);
                $('#id').val(data.id);
                $('#categoriaProduto').val(data.categoria_id);
                $('#dlgProdutos').modal('show')

            })
        }

        function remover(id) {
            $.ajax({
                type: 'DELETE',
                url: "/api/produtos/" + id,
                context: this,
                success: function () {
                    console.log('Apagado com sucesso');
                    let linhas = $('#tabelaProdutos>tbody>tr');

                    e = linhas.filter(function (i, elemento) {
                        return elemento.cells[0].textContent == id;
                    });
                    if (e)
                        e.remove();
                },
                error: function (error) {
                    console.error(error)
                }

            })
        }

        function carregarProdutos() {
            $.getJSON('/api/produtos', function (produto) {
                for (i = 0; produto.length; i++) {
                    let linhaProduto = montarLinha(produto[i]);
                    $('#tabelaProdutos>tbody').append(linhaProduto);
                }
            })
        }

        function criarProduto() {
            let prod = {
                nome: $('#nomeProduto').val(),
                preco: $('#precoProduto').val(),
                estoque: $('#estoqueProduto').val(),
                categoria_id: $('#categoriaProduto').val()
            }

            $.post('/api/produtos', prod, function (data) {
                let produto = JSON.parse(data);
                let linha = montarLinha(produto)
                $('#tabelaProdutos>tbody').append(linha)
            });
        }

        function salvarProduto(){
            let prod = {
                id: $('#id').val(),
                nome: $('#nomeProduto').val(),
                preco: $('#precoProduto').val(),
                estoque: $('#estoqueProduto').val(),
                categoria_id: $('#categoriaProduto').val(),
            };

            $.ajax({
                type: "PUT",
                url: '/api/produtos/'+prod.id,
                context: this,
                data: prod,
                success: function (data) {
                    console.log('Salvou OK');
                    prod = JSON.parse(data);
                    linhas = $('#tabelaProdutos>tbody>tr');
                    e =linhas.filter(function (i, elemento) {
                        return (elemento.cells[0].textContent == prod.id);
                    });
                    if(e){
                        e[0].cells[0].textContent = prod.id;
                        e[0].cells[1].textContent = prod.nome;
                        e[0].cells[2].textContent = prod. estoque;
                        e[0].cells[3].textContent = prod .preco;
                        e[0].cells[4].textContent = prod.categoria_id;
                    }

                },
                error: function (error) {
                    console.error(error)
                }
            })
        }

        $('#formProduto').submit(function (event) {
            event.preventDefault();
            if ($('#id').val() != '')
                salvarProduto();
            else
                criarProduto();

            $('#dlgProdutos').modal('hide');
        });

        $(function () {
            carregarCategorias()
            carregarProdutos()
        }).do
    </script>
@endsection