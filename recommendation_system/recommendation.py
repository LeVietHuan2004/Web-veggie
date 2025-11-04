import mysql.connector
from mysql.connector import Error
from sqlalchemy import create_engine
import pandas as pd
from flask import Flask, jsonify, request
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from nltk.tokenize import word_tokenize
pd.options.mode.chained_assignment = None

app = Flask(__name__)

#List stopwords Vietnamese

vietnamese_stopwords = [
    # Từ chung
    'các', 'và', 'là', 'muốn', 'đã', 'trong', 'khi', 'này', 'một',
    'những', 'được', 'với', 'cho', 'thì', 'tại', 'bởi', 'về', 'để',
    'nếu', 'sẽ', 'không', 'có', 'đây', 'đó', 'thấy', 'ra', 'phải',
    'ai', 'gì', 'nào', 'lại', 'hơn', 'như', 'vậy', 'chỉ', 'làm', 'lúc',

    # Từ marketing/không mang tính phân biệt trong ngữ cảnh rau củ
    'tươi', 'ngon', 'sạch', 'rẻ', 'giá', 'hàng', 'loại',
    'mua', 'bán', 'shop', 'siêu thị', 'cửa hàng', 'sản phẩm'
]


def create_connection():
    try:
        engine = create_engine('mysql+mysqlconnector://root:@localhost/veggie')
        print("Connect to Mysql success!")
        return engine
    except Error as e:
        print(f"Error when connect to Mysql: {e}")
        return None

def close_connection(engine):
    if engine:
        engine.dispose()
        print("Connection is closed.")

def fetch_products(engine):
    try:
        query = "SELECT * FROM products WHERE stock > 0;"
        df_products = pd.read_sql(query, engine)
        print("Get products data suceess!")
        return df_products
    except Error as e:
        print(f"Error when get products: {e}")
        return pd.DataFrame()

def combine_features(row):
    features = ['name', 'description', 'price', 'unit']
    return ' '.join(
        [str(row[feature]) for feature in features if feature in row and pd.notnull(row[feature])]
    )


@app.route('/api/product-recommendation', methods=['GET'])
def get_product_recommendations():
    engine = create_connection()
    if engine:
        products_df = fetch_products(engine)
        if not products_df.empty:
            # Check exist column
            required_columns = ['id', 'name', 'unit', 'description', 'price']
            for col in required_columns:
                if col not in products_df.columns:
                    return jsonify({"error": f"Column '{col}'  don't exist in data"}), 400
            # Merge attribute to column
            products_df['combineFeatures'] = products_df.apply(combine_features, axis = 1)

            # Calculate TF-IDF and cosine similarity
            tfidf = TfidfVectorizer()
            tfidf_matrix = tfidf.fit_transform(products_df['combineFeatures'])
            cosine_sim = cosine_similarity(tfidf_matrix, tfidf_matrix)

            # Get id of product from query parameter
            product_id = request.args.get('product_id')

            # Check if product_id is valid
            if not product_id or not product_id.isdigit():
                return jsonify({"error": "Invalid or missing 'id' parameter"}), 400

            product_id = int(product_id)

            # Check if product_id exists in column 'id' of DataFrame
            product_index = products_df[products_df['id'] == product_id].index[0]

            # Caculate point similarity for product selected
            sim_scores = list(enumerate(cosine_sim[product_index]))
            sim_scores_sorted = sorted(sim_scores, key=lambda x: x[1], reverse=True)[1:7]  # Get the 6 most similar products

            # Get index the 6 most similar products
            similar_products_indices = [i[0] for i in sim_scores_sorted]

            # Prepare response data
            related_products = products_df.iloc[similar_products_indices]
            related_products_list = related_products['id'].tolist()

            close_connection(engine)
            return jsonify({"related_products": related_products_list})
    return jsonify({"error" : "Unable to connect to database"}), 500

# Function pre process text
def preprocess_text(text):
    text = text.lower()

    # tokenize text
    tokens = word_tokenize(text)

    # remove stopwords
    processed_tokens = [word for word in tokens if word not in vietnamese_stopwords and word.isalnum()]

    # merge to string
    return  ' '.join(processed_tokens)

@app.route('/api/search-products', methods=['GET'])
def search_products():
    user_input = request.args.get('keyword')

    if not user_input:
        return jsonify({'error':"Missing search query"}), 4000

    engine = create_connection()

    try:
        products_df = fetch_products(engine)
        if products_df.empty:
            return jsonify({"Error": "No products found"}), 400

        products_df['combineFeatures'] = products_df.apply(combine_features, axis=1)

        products_df['processedFeatures'] = products_df['combineFeatures'].apply(preprocess_text)
        processed_input = preprocess_text(user_input)

        # Calculate TF-IDF and cosine similarity
        tfidf = TfidfVectorizer()
        tfidf_matrix = tfidf.fit_transform(products_df['processedFeatures'])
        input_vector = tfidf.transform([processed_input])

        cosine_sim = cosine_similarity(input_vector, tfidf_matrix)

        sim_scores = list(enumerate(cosine_sim[0]))
        sim_scores_sorted = sorted(sim_scores, key=lambda x: x[1], reverse=True)[:8]  # Get the 8 most similar products

        # Get index the 6 most similar products
        similar_products_indices = [i[0] for i in sim_scores_sorted]

        # Prepare response data
        related_products = products_df.iloc[similar_products_indices]
        related_products_list = related_products['id'].tolist()

        close_connection(engine)
        return jsonify({"related_products": related_products_list})

    except Exception as e:
        return jsonify({"error": f"An error occurred: {e}"}), 500


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5555, debug=True)