<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Rapport des Articles</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Helvetica, Arial, sans-serif; font-size: 16px; color: #333; }
  .wrapper { width: 92%; margin: 0 auto; }
  .header { text-align: center; margin-bottom: 14px; padding-bottom: 8px; border-bottom: 2px solid #2e6350; }
  .header h1 { font-size: 32px; color: #2e6350; letter-spacing: 1px; }
  .header .subtitle { font-size: 16px; color: #888; margin-top: 3px; }
  .meta { font-size: 14px; color: #999; margin-top: 2px; }
  table { width: 100%; border-collapse: collapse; margin-top: 6px; }
  th { background-color: #2e6350; color: #fff; padding: 10px 12px; text-align: left; font-weight: 600; font-size: 15px; }
  td { padding: 9px 12px; border-bottom: 1px solid #e0e0e0; font-size: 15px; }
  tr:nth-child(even) { background-color: #f5f8f6; }
  tr:nth-child(odd) { background-color: #fff; }
  .col-right { text-align: right; white-space: nowrap; }
  .footer { margin-top: 14px; text-align: center; font-size: 13px; color: #aaa; border-top: 1px solid #ddd; padding-top: 6px; }
  .summary { margin-bottom: 8px; font-size: 15px; color: #555; }
  .summary strong { color: #2e6350; }
</style>
</head>
<body>

<div class="wrapper">

<div class="header">
  <h1>STOCKFLOW</h1>
  <div class="subtitle">RAPPORT DES ARTICLES</div>
  <div class="meta">Généré le {{ now()->format('d/m/Y à H:i') }}</div>
</div>

<div class="summary">
  <strong>{{ $articles->count() }}</strong> article(s) au total
</div>

<table>
  <thead>
    <tr>
      <th style="width:8%">Référence</th>
      <th style="width:20%">Article</th>
      <th style="width:11%">Catégorie</th>
      <th style="width:18%">Description</th>
      <th class="col-right" style="width:11%">Prix Achat</th>
      <th class="col-right" style="width:11%">Prix Vente</th>
      <th class="col-right" style="width:8%">Stock Actuel</th>
      <th class="col-right" style="width:8%">Stock Min</th>
      <th style="width:7%">Unité</th>
    </tr>
  </thead>
  <tbody>
    @forelse($articles as $article)
      <tr>
        <td>{{ $article->reference }}</td>
        <td>{{ $article->nom }}</td>
        <td>{{ $article->categorie->nom ?? '—' }}</td>
        <td>{{ $article->description ?: '—' }}</td>
        <td class="col-right">{{ number_format($article->prix_achat, 0, ',', ' ') }} FCFA</td>
        <td class="col-right">{{ number_format($article->prix_vente, 0, ',', ' ') }} FCFA</td>
        <td class="col-right">{{ $article->stock_actuel }}</td>
        <td class="col-right">{{ $article->stock_minimum }}</td>
        <td>{{ $article->unite }}</td>
      </tr>
    @empty
      <tr>
        <td colspan="9" style="text-align:center; padding:15px; color:#999;">Aucun article trouvé.</td>
      </tr>
    @endforelse
  </tbody>
</table>

<div class="footer">
  StockFlow — Rapport automatique
</div>

</div>

</body>
</html>
