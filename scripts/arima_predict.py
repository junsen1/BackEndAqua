import sys
import json
import pandas as pd
from statsmodels.tsa.arima.model import ARIMA

def predict_arima(data_str):
    # Parse the JSON input
    data_dict = json.loads(data_str)

    # Convert input data to a DataFrame
    df = pd.DataFrame(data_dict)
    
    # Iterate over columns (fields) to predict values
    predicted_values = {}
    for column in df.columns:
        if column != "created_at":
            values = df[column].astype(float)
            
            # Build ARIMA model
            # You need to adjust these parameters according to your data and model choice
            order = (1, 1, 0)  # ARIMA order (p, d, q)
            model = ARIMA(values, order=order)
            model_fit = model.fit()
            # print(model_fit.forecast())

            # Make prediction for the next value
            predicted_value = model_fit.forecast()
            first_forecasted_value = predicted_value.iloc[0]
            # print(first_forecasted_value)
            predicted_values[column] = first_forecasted_value
    
    return predicted_values

if __name__ == "__main__":
    input_data = sys.argv[1]
    predicted_value = predict_arima(input_data)
    
    # Convert the predicted_value dictionary to JSON format
    predicted_value_json = json.dumps(predicted_value)
    
    # Print the JSON result
    print(predicted_value_json)
