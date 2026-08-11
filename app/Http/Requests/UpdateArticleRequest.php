<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $articleId = $this->route('article')?->id;

        return [
            'name'               => 'required|string|max:255',
            'description'        => 'nullable|string',
            'prix'               => 'required|numeric|min:0',
            'prix_promotionnel'  => 'nullable|numeric|min:0|lt:prix',
            'quantite'           => 'required|integer|min:0',
            'category_id'        => 'nullable|exists:categories,id',
            'fournisseur_id'     => 'nullable|exists:fournisseurs,id',
            'emplacement_id'     => 'nullable|exists:emplacements,id',
            'sku'                => 'nullable|string|max:100|unique:articles,sku,' . $articleId,
            'image_principale'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'statut'             => 'nullable|string|in:disponible,brouillon,archivé,en_rupture_de_stock',
            'poids'              => 'nullable|numeric|min:0',
            'slug'               => 'nullable|string|max:255|unique:articles,slug,' . $articleId,
            'est_visible'        => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => "Le nom de l'article est obligatoire.",
            'name.max'                  => "Le nom de l'article ne doit pas dépasser 255 caractères.",
            'prix.required'             => "Le prix est obligatoire.",
            'prix.numeric'              => "Le prix doit être un nombre.",
            'prix.min'                  => "Le prix ne peut pas être négatif.",
            'prix_promotionnel.numeric' => "Le prix promotionnel doit être un nombre.",
            'prix_promotionnel.min'     => "Le prix promotionnel ne peut pas être négatif.",
            'prix_promotionnel.lt'      => "Le prix promotionnel doit être inférieur au prix normal.",
            'quantite.required'         => "La quantité est obligatoire.",
            'quantite.integer'          => "La quantité doit être un nombre entier.",
            'quantite.min'              => "La quantité ne peut pas être négative.",
            'category_id.exists'        => "La catégorie sélectionnée n'est pas valide.",
            'fournisseur_id.exists'     => "Le fournisseur sélectionné n'est pas valide.",
            'emplacement_id.exists'     => "L'emplacement sélectionné n'est pas valide.",
            'sku.unique'                => "Ce SKU existe déjà pour un autre article.",
            'image_principale.image'    => "Le fichier doit être une image.",
            'image_principale.mimes'    => "L'image doit être de type : jpeg, png, jpg, gif ou webp.",
            'image_principale.max'      => "L'image ne doit pas dépasser 2 Mo.",
            'statut.in'                 => "Le statut sélectionné n'est pas valide.",
            'slug.unique'               => "Ce slug existe déjà pour un autre article.",
            'est_visible.boolean'       => "La valeur du champ 'est visible' doit être vraie ou fausse.",
        ];
    }
}
